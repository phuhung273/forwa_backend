<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Product\Model\Plugin;

use EzLaunch\Core\Helper\ArrayHelper;
use EzLaunch\Core\Helper\CustomerHelper;
use EzLaunch\Quote\Api\CartItemRepositoryInterface;
use Magento\Authorization\Model\CompositeUserContext;
use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * EzLaunch Plugin to custom product api
 *
 */
class ProductPlugin
{
    const DEFAULT_STORE_ID = 1;

    /** 
     * @var ProductExtensionFactory 
     */
    private $productExtensionFactory;

    /** 
     * @var \Magento\Catalog\Helper\Image 
     */
    private $imageHelper;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CompositeUserContext
     */
    private $userContext;

    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @var GetSalableQuantityDataBySku
     */
    private $getSalableQuantityDataBySku;


    /**
     * Constructor.
     * @param ProductExtensionFactory $productExtensionFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param ArrayHelper $arrayHelper
     * @param CustomerHelper $customerHelper
     * @param LoggerInterface $logger
     * @param CompositeUserContext $userContext
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     */
    public function __construct(
        ProductExtensionFactory $productExtensionFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Store\Model\App\Emulation $appEmulation,
        WebsiteRepositoryInterface $websiteRepository,
        ArrayHelper $arrayHelper,
        CustomerHelper $customerHelper,
        LoggerInterface $logger,
        CompositeUserContext $userContext,
        CartItemRepositoryInterface $cartItemRepository,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ){
        $this->productExtensionFactory = $productExtensionFactory;
        $this->imageHelper = $imageHelper;
        $this->appEmulation = $appEmulation;
        $this->websiteRepository = $websiteRepository;
        $this->arrayHelper = $arrayHelper;
        $this->customerHelper = $customerHelper;
        $this->logger = $logger;
        $this->userContext = $userContext;
        $this->cartItemRepository = $cartItemRepository;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
    }
    
    /**
     * Modify result from ProductRepositoryInterface::getList
     * 
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Catalog\Api\Data\ProductSearchResultsInterface $searchResults
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function afterGetList(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductSearchResultsInterface $searchResults
    ){
        $this->appEmulation->startEnvironmentEmulation(self::DEFAULT_STORE_ID, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $products = [];
        foreach ($searchResults->getItems() as $entity) {
    
            $entity = $this->modifyProduct($subject, $entity);
    
            $products[] = $entity;
        }
        $this->appEmulation->stopEnvironmentEmulation();
        $searchResults->setItems($products);
        return $searchResults;
    }

    /**
     * Modify result from ProductRepositoryInterface::get
     * 
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ){
        $this->appEmulation->startEnvironmentEmulation(self::DEFAULT_STORE_ID, \Magento\Framework\App\Area::AREA_FRONTEND, true);
    
        $extensionAttributes = $product->getExtensionAttributes();

        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->productExtensionFactory->create();
        }

        $product = $subject->getById($product->getId());

        $this->collectBaseImages($product, $extensionAttributes);
        $this->collectQuantity($product, $extensionAttributes);
        $this->collectSeller($product, $extensionAttributes);
        $this->collectIsDisabled($product, $extensionAttributes);
        
        $product->setExtensionAttributes($extensionAttributes);
            
        $this->appEmulation->stopEnvironmentEmulation();
        return $product;
    }
    
    /**
     * Modify product
     * 
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    private function modifyProduct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, 
        \Magento\Catalog\Api\Data\ProductInterface $product
    ){
        $extensionAttributes = $product->getExtensionAttributes();

        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->productExtensionFactory->create();
        }

        $product = $productRepository->getById($product->getId());

        $this->collectBaseImages($product, $extensionAttributes);
        $this->collectQuantity($product, $extensionAttributes);
        $this->collectSeller($product, $extensionAttributes);
        
        $product->setExtensionAttributes($extensionAttributes);

        return $product;
    }
    
    /**
     * Set extensionAttributes base_image_urls
     *
     * @param  ProductInterface $product
     * @param  ExtensionAttributesInterface $extensionAttributes
     * @return void
     */
    private function collectBaseImages(ProductInterface $product, ExtensionAttributesInterface $extensionAttributes){
        $urls = [];
        foreach($product->getMediaGalleryEntries() as $image){
            $baseImage = $this->imageHelper->init($product, 'product_base_image')
                ->setImageFile($image->getFile())
                ->getUrl();

            $urls[] = $baseImage;
        }
        $extensionAttributes->setBaseImageUrls($urls);
    }
    
    /**
     * Set extensionAttributes quantity
     *
     * @param  ProductInterface $product
     * @param  ExtensionAttributesInterface $extensionAttributes
     * @return void
     */
    private function collectQuantity(ProductInterface $product, ExtensionAttributesInterface $extensionAttributes){
        $salable = $this->getSalableQuantityDataBySku->execute($product->getSku());
        $extensionAttributes->setQuantity((int)$salable[0]['qty']);
    }
    
    /**
     * Set extensionAttributes seller
     *
     * @param  ProductInterface $product
     * @param  ExtensionAttributesInterface $extensionAttributes
     * @return void
     */
    private function collectSeller(ProductInterface $product, ExtensionAttributesInterface $extensionAttributes){
        $websiteId = $this->arrayHelper->getFirstNonDefaultIdOrNull($product->getWebsiteIds());

        if (!isset($websiteId)) {
            $this->logger->debug('No website id');
            return;
        }

        $storeIds = $this->websiteRepository->getById($websiteId)->getStoreIds();
        $storeId = $this->arrayHelper->getFirstNonDefaultIdOrNull($storeIds);
        if (!isset($storeId)) {
            $this->logger->debug('No store id');
            return;
        }

        $customer = $this->customerHelper->getByStoreId($storeId);

        if (!isset($customer)) {
            $this->logger->debug('No customer');
            return;
        }

        $extensionAttributes->setSellerName($customer->getName());

        $customerId = $this->userContext->getUserId();
        $extensionAttributes->setIsDisabled($customerId == null);
    }

    /**
     * Set extensionAttributes is disabled
     *
     * @param  ProductInterface $product
     * @param  ExtensionAttributesInterface $extensionAttributes
     * @return void
     */
    private function collectIsDisabled(ProductInterface $product, ExtensionAttributesInterface $extensionAttributes){
        $customerId = $this->userContext->getUserId();

        if ($customerId == null) {
            $extensionAttributes->setIsDisabled(false);
        } else {
            $cartItems = $this->cartItemRepository->getListOfCustomerContain($customerId, $product->getId());
            // $this->logger->debug('Number of items: ' . count($cartItems));
            $extensionAttributes->setIsDisabled(count($cartItems) > 0);
        }
    }
}
