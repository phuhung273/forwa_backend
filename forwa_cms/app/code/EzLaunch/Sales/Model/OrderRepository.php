<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace EzLaunch\Sales\Model;

use EzLaunch\Sales\Api\OrderRepositoryInterface;
use EzLaunch\Core\Helper\ArrayHelper;
use EzLaunch\Core\Helper\CustomerHelper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Tax\Api\OrderTaxManagementInterface;
use Magento\Payment\Api\Data\PaymentAdditionalInfoInterfaceFactory;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Repository class
 *
 */
class OrderRepository extends \Magento\Sales\Model\OrderRepository implements OrderRepositoryInterface
{

    const DEFAULT_STORE_ID = 1;

    /** 
     * @var \Magento\Catalog\Helper\Image 
     */
    private $imageHelper;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;
    
    /**
     *
     * @var \EzLaunch\Sales\Model\OrderSearchResultsFactory
     */
    private $searchResultsFactory;

    /**
     *
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * Constructor
     *
     * @param Metadata $metadata
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory $orderSearchResultFactory
     * @param \EzLaunch\Sales\Model\OrderSearchResultsFactory $searchResultsFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param ArrayHelper $arrayHelper
     * @param CustomerHelper $customerHelper
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory|null $orderExtensionFactory
     * @param OrderTaxManagementInterface|null $orderTaxManagement
     * @param PaymentAdditionalInfoInterfaceFactory|null $paymentAdditionalInfoFactory
     * @param JsonSerializer|null $serializer
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        Metadata $metadata,
        \Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory $orderSearchResultFactory,
        \EzLaunch\Sales\Model\OrderSearchResultsFactory $searchResultsFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Store\Model\App\Emulation $appEmulation,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        WebsiteRepositoryInterface $websiteRepository,
        ArrayHelper $arrayHelper,
        CustomerHelper $customerHelper,
        CollectionProcessorInterface $collectionProcessor = null,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory = null,
        OrderTaxManagementInterface $orderTaxManagement = null,
        PaymentAdditionalInfoInterfaceFactory $paymentAdditionalInfoFactory = null,
        JsonSerializer $serializer = null,
        JoinProcessorInterface $extensionAttributesJoinProcessor = null
    ) {
        parent::__construct(
            $metadata,
            $orderSearchResultFactory,
            $logger,
            $collectionProcessor,
            $orderExtensionFactory,
            $orderTaxManagement,
            $paymentAdditionalInfoFactory,
            $serializer,
            $extensionAttributesJoinProcessor
        );

        $this->searchResultsFactory = $searchResultsFactory;
        $this->imageHelper = $imageHelper;
        $this->appEmulation = $appEmulation;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->websiteRepository = $websiteRepository;
        $this->arrayHelper = $arrayHelper;
        $this->customerHelper = $customerHelper;
    }

    /**
     * @inheritdoc
     */
    public function getCustomList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $orderSearchResults = parent::getList($searchCriteria);

        $this->appEmulation->startEnvironmentEmulation(self::DEFAULT_STORE_ID, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        
        $items = [];
        foreach ($orderSearchResults->getItems() as $entity) {
    
            $entity = $this->modifyItem($entity);
    
            $items[] = $entity;
        }
        $this->appEmulation->stopEnvironmentEmulation();

        // Cannot setItems on existing \Magento\Sales\Api\Data\OrderSearchResultInterface
        // => create another result instance
        $results = $this->searchResultsFactory->create();
        $results->setItems($items);

        return $results;
    }

    /**
     * Modify item
     * 
     * @param OrderInterface $item
     * @return OrderInterface
     */
    private function modifyItem(OrderInterface $item){
        $extensionAttributes = $item->getExtensionAttributes();

        if (empty($extensionAttributes)) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $item = $this->get($item->getEntityId());
        $orderItems = $item->getItems();
        if(!isset($orderItems) || empty($orderItems)){

            $this->logger->debug('Empty order');
            
        } else {
            $product = $this->productRepository->getById(reset($orderItems)->getProductId());
    
            $this->collectBaseImages($product, $extensionAttributes);
            $this->collectSeller($product, $extensionAttributes);

            $item->setExtensionAttributes($extensionAttributes);
        }

        return $item;
    }
    
    /**
     * Set extensionAttributes base_image_urls
     *
     * @param  ProductInterface $product
     * @param  OrderExtensionInterface $extensionAttributes
     * @return void
     */
    private function collectBaseImages(ProductInterface $product, OrderExtensionInterface $extensionAttributes){
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
     * Set extensionAttributes seller
     *
     * @param  ProductInterface $product
     * @param  OrderExtensionInterface $extensionAttributes
     * @return void
     */
    private function collectSeller(ProductInterface $product, OrderExtensionInterface $extensionAttributes){
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
    }
}
