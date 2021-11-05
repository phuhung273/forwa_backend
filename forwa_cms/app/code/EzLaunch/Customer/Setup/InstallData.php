<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Setup;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    const LATITUDE_ATTRIBUTE_CODE = 'latitude';
    const LONGITUDE_ATTRIBUTE_CODE = 'longitude';

	/**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * Constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param Config $eavConfig
     */
	public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    ) {
		$this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		/** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->addAttribute($eavSetup, self::LATITUDE_ATTRIBUTE_CODE);
        $this->addAttribute($eavSetup, self::LONGITUDE_ATTRIBUTE_CODE);

	}

    private function addAttribute(EavSetup $eavSetup, string $attributeCode){
        $eavSetup->addAttribute(
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

        $customAttribute = $this->eavConfig->getAttribute(
            AddressMetadataInterface::ENTITY_TYPE_ADDRESS, 
            $attributeCode
        );

        $customAttribute->setData('used_in_forms', [
            'adminhtml_customer_address',
            'customer_address_edit',
            'customer_register_address',
        ]);
        
        $customAttribute->save();
    }
}