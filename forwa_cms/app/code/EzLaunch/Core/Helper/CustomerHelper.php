<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Core\Helper;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class CustomerHelper extends AbstractHelper{

    const DEFAULT_ID = 1;

    /**
     * @var CollectionFactory
     */
    private $customerFactory;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Magento\Quote\Model\AddressFactory
     */
    private $quoteAddressFactory;

    /**
     * Constructor.
     * 
     * @param CollectionFactory $customerFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param \Magento\Quote\Model\AddressFactory $quoteAddressFactory
     */
    public function __construct(
        CollectionFactory $customerFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
    ){
        $this->customerFactory = $customerFactory;
        $this->addressRepository = $addressRepository;
        $this->quoteAddressFactory= $quoteAddressFactory;
    }
    
    /**
     * Get customer by storeId
     *
     * @param int $storeId
     * @return \Magento\Customer\Model\Customer
     */
    public function getByStoreId(int $storeId) {
        // TODO: catch exception multiple customer in store & no customer
        return $this->customerFactory->create()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("store_id", array("eq" => $storeId))
                ->load()
                ->getFirstItem();
    }
    
    /**
     * Get customer default billing as quote address
     *
     * @param  CustomerInterface $customer
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    public function getDefaultQuoteBilling(CustomerInterface $customer){
        $customerAddress = $this->addressRepository->getById($customer->getDefaultBilling());

        $quoteAddress = $this->quoteAddressFactory->create();

        $region = $customerAddress->getRegion();
        $quoteAddress->setRegion($region->getRegion());
        $quoteAddress->setRegionId($region->getRegionId());
        $quoteAddress->setRegionCode($region->getRegionCode());

        $quoteAddress->setCountryId($customerAddress->getCountryId());
        $quoteAddress->setStreet($customerAddress->getStreet());
        $quoteAddress->setPostcode($customerAddress->getPostcode());
        $quoteAddress->setCity($customerAddress->getCity());
        $quoteAddress->setFirstName($customerAddress->getFirstName());
        $quoteAddress->setLastName($customerAddress->getLastName());
        $quoteAddress->setEmail($customer->getEmail());
        $quoteAddress->setTelephone($customerAddress->getTelephone());

        $quoteAddress->setCustomerId($customer->getId());

        return $quoteAddress;
    }

    /**
     * Get customer default billing as quote address
     *
     * @param  CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getDefaultBilling(CustomerInterface $customer){
        return $this->addressRepository->getById($customer->getDefaultBilling());
    }
}