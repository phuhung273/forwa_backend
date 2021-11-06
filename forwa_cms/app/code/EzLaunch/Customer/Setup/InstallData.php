<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    const LATITUDE_ATTRIBUTE_CODE = 'latitude';
    const LONGITUDE_ATTRIBUTE_CODE = 'longitude';

	/**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * Constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
	public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
		$this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		/** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        // $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $this->addLatLong($customerSetup, self::LATITUDE_ATTRIBUTE_CODE, $attributeSetId, $attributeGroupId);
        $this->addLatLong($customerSetup, self::LONGITUDE_ATTRIBUTE_CODE, $attributeSetId, $attributeGroupId);

	}

    private function addLatLong(CustomerSetup $customerSetup, string $attributeCode, $attributeSetId, $attributeGroupId){
        $customerSetup->addAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            $attributeCode,
            [
                'type'          => 'varchar',
                'label'         => ucfirst($attributeCode),
                'input'         => 'text',
                'required'      => false,
                'visible'       => true,
                'user_defined'  => true,
                'sort_order'    => 100,
                'system'        => false
            ]
        );

        $customAttribute = $customerSetup->getEavConfig()->getAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            $attributeCode
        );

        $customAttribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
            ],
        ]);
        
        $customAttribute->save();
    }
}