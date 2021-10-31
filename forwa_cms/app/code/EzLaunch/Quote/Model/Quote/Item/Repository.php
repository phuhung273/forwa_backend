<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Quote\Model\Quote\Item;

use EzLaunch\Quote\Api\CartItemRepositoryInterface;
use EzLaunch\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as CartItemCollectionFactory;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use Magento\Quote\Model\Quote\Item\Repository as MagentoCartItemRepository;
use Psr\Log\LoggerInterface;

/**
 * Repository for quote item.
 */
class Repository extends MagentoCartItemRepository implements CartItemRepositoryInterface
{

    const DEFAULT_SHIPPING_METHOD = 'flatrate';

    const ORDER_STATUS_PENDING  = 'pending';

    /**
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    
    /**
     *
     * @var ShippingInformationManagementInterface
     */
    private $shippingInformationManagement;

    /**
     *
     * @var \Magento\Checkout\Model\ShippingInformationFactory
     */
    private $shippingInformationFactory;

    /**
     *
     * @var PaymentInformationManagementInterface
     */
    private $paymentInformationManagement;

    /**
     * @var \Magento\Quote\Model\Quote\PaymentFactory
     */
    protected $quotePaymentFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \EzLaunch\Core\Helper\CustomerHelper
     */
    protected $customerHelper;

    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var CartItemCollectionFactory
     */
    protected $cartItemCollectionFactory;

    /**
     * @var \EzLaunch\Quote\Model\CartItemSearchResultsFactory
     */
    protected $cartItemSearchResultsFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    private $addressesToSync = [];

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param ProductRepositoryInterface $productRepository
     * @param CartItemInterfaceFactory $itemDataFactory
     * @param CartItemOptionsProcessor $cartItemOptionsProcessor
     * @param CustomerRepositoryInterface $customerRepository
     * @param ShippingInformationManagementInterface $shippingInformationManagement
     * @param GuestPaymentInformationManagementInterface $guestPaymentInformationManagement
     * @param \Magento\Checkout\Model\ShippingInformationFactory $shippingInformationFactory
     * @param \Magento\Quote\Model\Quote\PaymentFactory $quotePaymentFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param PaymentInformationManagementInterface $paymentInformationManagement
     * @param \EzLaunch\Core\Helper\CustomerHelper $customerHelper
     * @param AddressRepositoryInterface $addressRepository
     * @param CartItemCollectionFactory $cartItemCollectionFactory
     * @param \EzLaunch\Quote\Model\CartItemSearchResultsFactory $cartItemSearchResultsFactory
     * @param LoggerInterface $logger
     * @param CartItemProcessorInterface[] $cartItemProcessors
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ProductRepositoryInterface $productRepository,
        CartItemInterfaceFactory $itemDataFactory,
        CartItemOptionsProcessor $cartItemOptionsProcessor,
        CustomerRepositoryInterface $customerRepository,
        ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Checkout\Model\ShippingInformationFactory $shippingInformationFactory,
        \Magento\Quote\Model\Quote\PaymentFactory $quotePaymentFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        PaymentInformationManagementInterface $paymentInformationManagement,
        \EzLaunch\Core\Helper\CustomerHelper $customerHelper,
        AddressRepositoryInterface $addressRepository,
        CartItemCollectionFactory $cartItemCollectionFactory,
        \EzLaunch\Quote\Model\CartItemSearchResultsFactory $cartItemSearchResultsFactory,
        LoggerInterface $logger,
        array $cartItemProcessors = []
    ) {
        parent::__construct(
            $quoteRepository,
            $productRepository, 
            $itemDataFactory, 
            $cartItemOptionsProcessor,
            $cartItemProcessors
        );

        $this->customerRepository = $customerRepository;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->shippingInformationFactory = $shippingInformationFactory;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->quotePaymentFactory = $quotePaymentFactory;
        $this->customerHelper = $customerHelper;
        $this->quoteFactory = $quoteFactory;
        $this->addressRepository = $addressRepository;
        $this->cartItemCollectionFactory = $cartItemCollectionFactory;
        $this->cartItemSearchResultsFactory = $cartItemSearchResultsFactory;
        $this->logger = $logger;
    }
    
    /**
     * @inheritdoc
     */
    public function addToCartCreateOrder(\Magento\Quote\Api\Data\CartItemInterface $cartItem, $message, $customerId)
    {
        $cartId = $cartItem->getQuoteId();
        if (!$cartId) {
            $customer = $this->customerRepository->getById($customerId);
            $cartId = $this->createEmptyCartForCustomer($customer->getId(), $customer->getStoreId());
        }

        $quote = $this->quoteRepository->getActive($cartId);
        $quoteItems = $quote->getItems();
        $quoteItems[] = $cartItem;
        $quote->setItems($quoteItems);
        $quote->setCustomerNote($message);
        $this->quoteRepository->save($quote);
        $quote->collectTotals();

        $shippingInfo = $this->shippingInformationFactory->create();

        $defaultQuoteBilling = $this->customerHelper->getDefaultQuoteBilling($customer);
        $shippingInfo->setShippingAddress($defaultQuoteBilling);
        $shippingInfo->setBillingAddress($defaultQuoteBilling);
        $shippingInfo->setShippingCarrierCode(self::DEFAULT_SHIPPING_METHOD); // What is this ?
        $shippingInfo->setShippingMethodCode(self::DEFAULT_SHIPPING_METHOD); // What is this ?
        $paymentDetail = $this->shippingInformationManagement->saveAddressInformation($quote->getId(), $shippingInfo);

        $paymentMethod = $this->quotePaymentFactory->create();
        $paymentMethod->setMethod($paymentDetail->getPaymentMethods()[0]->getCode());

        $orderId = $this->paymentInformationManagement->savePaymentInformationAndPlaceOrder(
            $quote->getId(),
            $paymentMethod,
            $defaultQuoteBilling,
        );

        return $orderId;
    }

    /**
     * @inheritdoc
     */
    public function getListContain($productId)
    {
        $collection = $this->cartItemCollectionFactory->create();
        $collection->addCustomerInfo($productId);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        $searchResult = $this->cartItemSearchResultsFactory->create();
        $searchResult->setItems($items);

        return $searchResult;
    }

    /**
     * @inheritdoc
     */
    public function getListOfCustomerContain($customerId, $productId)
    {
        $collection = $this->cartItemCollectionFactory->create();
        $collection->filterCustomerProduct($customerId, $productId);

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        return $items;
    }

    /**
     * Creates an empty cart and quote for a specified customer if customer does not have a cart yet.
     *
     * @param int $customerId The customer ID.
     * @return int new cart ID if customer did not have a cart or ID of the existing cart otherwise.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The empty cart and quote could not be created.
     */
    private function createEmptyCartForCustomer($customerId, $storeId)
    {
        $quote = $this->createCustomerCart($customerId, $storeId);

        $this->_prepareCustomerQuote($quote);

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("The quote can't be created."));
        }
        return (int)$quote->getId();
    }

    /**
     * Creates a cart for the currently logged-in customer.
     *
     * @param int $customerId
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote Cart object.
     * @throws CouldNotSaveException The cart could not be created.
     */
    protected function createCustomerCart($customerId, $storeId)
    {
        try {
            $quote = $this->quoteRepository->getActiveForCustomer($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customer = $this->customerRepository->getById($customerId);
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($storeId);
            $quote->setCustomer($customer);
            $quote->setCustomerIsGuest(0);
        }
        return $quote;
    }

    /**
     * Prepare address for customer quote.
     *
     * @param Quote $quote
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareCustomerQuote($quote)
    {
        /** @var Quote $quote */
        $billing = $quote->getBillingAddress();
        $shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->customerRepository->getById($quote->getCustomerId());
        $hasDefaultBilling = (bool)$customer->getDefaultBilling();
        $hasDefaultShipping = (bool)$customer->getDefaultShipping();

        if ($shipping && !$shipping->getSameAsBilling()
            && (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())
        ) {
            if ($shipping->getQuoteId()) {
                $shippingAddress = $shipping->exportCustomerAddress();
            } else {
                $defaultShipping = $this->customerRepository->getById($customer->getId())->getDefaultShipping();
                if ($defaultShipping) {
                    try {
                        $shippingAddress = $this->addressRepository->getById($defaultShipping);
                    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
                    } catch (LocalizedException $e) {
                        // no address
                    }
                }
            }
            if (isset($shippingAddress)) {
                if (!$hasDefaultShipping) {
                    //Make provided address as default shipping address
                    $shippingAddress->setIsDefaultShipping(true);
                    $hasDefaultShipping = true;
                    if (!$hasDefaultBilling && !$billing->getSaveInAddressBook()) {
                        $shippingAddress->setIsDefaultBilling(true);
                        $hasDefaultBilling = true;
                    }
                }
                //save here new customer address
                $shippingAddress->setCustomerId($quote->getCustomerId());
                $this->addressRepository->save($shippingAddress);
                $quote->addCustomerAddress($shippingAddress);
                $shipping->setCustomerAddressData($shippingAddress);
                $this->addressesToSync[] = $shippingAddress->getId();
                $shipping->setCustomerAddressId($shippingAddress->getId());
            }
        }

        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            if ($billing->getQuoteId()) {
                $billingAddress = $billing->exportCustomerAddress();
            } else {
                $defaultBilling = $this->customerRepository->getById($customer->getId())->getDefaultBilling();
                if ($defaultBilling) {
                    try {
                        $billingAddress = $this->addressRepository->getById($defaultBilling);
                    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
                    } catch (LocalizedException $e) {
                        // no address
                    }
                }
            }
            if (isset($billingAddress)) {
                if (!$hasDefaultBilling) {
                    //Make provided address as default shipping address
                    if (!$hasDefaultShipping) {
                        //Make provided address as default shipping address
                        $billingAddress->setIsDefaultShipping(true);
                    }
                    $billingAddress->setIsDefaultBilling(true);
                }
                $billingAddress->setCustomerId($quote->getCustomerId());
                $this->addressRepository->save($billingAddress);
                $quote->addCustomerAddress($billingAddress);
                $billing->setCustomerAddressData($billingAddress);
                $this->addressesToSync[] = $billingAddress->getId();
                $billing->setCustomerAddressId($billingAddress->getId());
            }
        }
        if ($shipping && !$shipping->getCustomerId() && !$hasDefaultBilling) {
            $shipping->setIsDefaultBilling(true);
        }
    }
}
