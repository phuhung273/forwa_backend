<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class UpgradeData implements UpgradeDataInterface, PatchVersionInterface
{

    const VERSION = '1.0.2';

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

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), self::VERSION, '<')) {
            $setup->startSetup();
        
            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

            // $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

            $this->updateLatLong($customerSetup, self::LATITUDE_ATTRIBUTE_CODE, $attributeSetId, $attributeGroupId);
            $this->updateLatLong($customerSetup, self::LONGITUDE_ATTRIBUTE_CODE, $attributeSetId, $attributeGroupId);

            $setup->endSetup();
        }
    }

    private function updateLatLong(CustomerSetup $customerSetup, string $attributeCode, $attributeSetId, $attributeGroupId){
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
            $attributeCode
        )
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
            ],
        ]);

        $attribute->save();
    }

    public static function getVersion()
    {
        return self::VERSION;
    }
}
