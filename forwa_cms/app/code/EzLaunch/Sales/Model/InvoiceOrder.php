<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Sales\Model;

use EzLaunch\FirebaseCloudMessaging\Api\FcmServiceInterface;
use EzLaunch\Sales\Api\InvoiceOrderInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\Invoice\NotifierInterface;
use Magento\Sales\Model\Order\InvoiceDocumentFactory;
use Magento\Sales\Model\Order\InvoiceRepository;
use Magento\Sales\Model\Order\OrderStateResolverInterface;
use Magento\Sales\Model\Order\PaymentAdapterInterface;
use Magento\Sales\Model\Order\Validation\InvoiceOrderInterface as InvoiceOrderValidator;
use Psr\Log\LoggerInterface;

/**
 * Class InvoiceOrder
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InvoiceOrder extends \Magento\Sales\Model\InvoiceOrder implements InvoiceOrderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FcmServiceInterface
     */
    private $fcmService;

    /**
     * @var \EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken\CollectionFactory
     */
    private $firebaseTokenCollectionFactory;

    /**
     * InvoiceOrder constructor.
     * @param ResourceConnection $resourceConnection
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceDocumentFactory $invoiceDocumentFactory
     * @param PaymentAdapterInterface $paymentAdapter
     * @param OrderStateResolverInterface $orderStateResolver
     * @param OrderConfig $config
     * @param InvoiceRepository $invoiceRepository
     * @param InvoiceOrderValidator $invoiceOrderValidator
     * @param NotifierInterface $notifierInterface
     * @param LoggerInterface $logger
     * @param FcmServiceInterface $fcmService
     * @param \EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken\CollectionFactory $firebaseTokenCollectionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        OrderRepositoryInterface $orderRepository,
        InvoiceDocumentFactory $invoiceDocumentFactory,
        PaymentAdapterInterface $paymentAdapter,
        OrderStateResolverInterface $orderStateResolver,
        OrderConfig $config,
        InvoiceRepository $invoiceRepository,
        InvoiceOrderValidator $invoiceOrderValidator,
        NotifierInterface $notifierInterface,
        LoggerInterface $logger,
        FcmServiceInterface $fcmService,
        \EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken\CollectionFactory $firebaseTokenCollectionFactory
    ) {
        parent::__construct(
            $resourceConnection,
            $orderRepository,
            $invoiceDocumentFactory,
            $paymentAdapter,
            $orderStateResolver,
            $config,
            $invoiceRepository,
            $invoiceOrderValidator,
            $notifierInterface,
            $logger
        );

        $this->orderRepository = $orderRepository;
        $this->fcmService = $fcmService;
        $this->firebaseTokenCollectionFactory = $firebaseTokenCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice($orderId, $productName) 
    {
        $invoiceId = parent::execute($orderId);

        $order = $this->orderRepository->get($orderId);
        $customerId = $order->getCustomerId();

        $collection = $this->firebaseTokenCollectionFactory->create();
        $collection->addCustomerInfo($customerId);

        $message = "Bạn đã được chọn, mau tới lấy {$productName}!";

        foreach ($collection as $item) {
            $this->fcmService->send(
                $item->getValue(),
                null,
                $message
            );
        }

        return $invoiceId;
    }
}
