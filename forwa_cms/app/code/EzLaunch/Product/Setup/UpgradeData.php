<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Product\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class UpgradeData implements UpgradeDataInterface, PatchVersionInterface
{

    const VERSION = '1.0.3';

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * AddSwatchImageAttribute constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), self::VERSION, '<')) {
            $setup->startSetup();
        
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'pickup_time',
                'is_required',
                0
            );

            $setup->endSetup();
        }

        
    }

    public static function getVersion()
    {
        return self::VERSION;
    }
}
