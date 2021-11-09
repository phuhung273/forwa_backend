<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Core\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Store\Api\WebsiteRepositoryInterface;

class ProductHelper extends AbstractHelper{

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * Constructor.
     * 
     * @param ArrayHelper $arrayHelper
     * @param CustomerHelper $customerHelper
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        CustomerHelper $customerHelper,
        WebsiteRepositoryInterface $websiteRepository,
        ProductRepositoryInterface $productRepository
    ){
        $this->arrayHelper = $arrayHelper;
        $this->customerHelper = $customerHelper;
        $this->websiteRepository = $websiteRepository;
        $this->productRepository = $productRepository;
    }
    
    /**
     * Get seller of product
     *
     * @param  ProductInterface $product
     * @return CustomerInterface|null
     */
    public function getSellerOfProduct(ProductInterface $product){
        $websiteId = $this->arrayHelper->getFirstNonDefaultIdOrNull($product->getWebsiteIds());

        if (!isset($websiteId)) {
            return null;
        }

        $storeIds = $this->websiteRepository->getById($websiteId)->getStoreIds();
        $storeId = $this->arrayHelper->getFirstNonDefaultIdOrNull($storeIds);
        if (!isset($storeId)) {
            return null;
        }

        return $this->customerHelper->getByStoreId($storeId);
    }

    /**
     * Get seller of product id
     *
     * @param  int $productId
     * @return CustomerInterface|null
     */
    public function getSellerOfProductId($productId){
        $product = $this->productRepository->getById($productId);
        return $this->getSellerOfProduct($product);
    }

    /**
     * Get seller of product sku
     *
     * @param string $sku
     * @return CustomerInterface|null
     */
    public function getSellerOfProductSku($sku){
        $product = $this->productRepository->get($sku);
        return $this->getSellerOfProduct($product);
    }
}