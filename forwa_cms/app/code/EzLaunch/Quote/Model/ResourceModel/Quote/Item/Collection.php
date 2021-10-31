<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace EzLaunch\Quote\Model\ResourceModel\Quote\Item;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use EzLaunch\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\ResourceModel\Quote\Item as ResourceQuoteItem;

/**
 * Quote item resource collection
 *
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(QuoteItem::class, ResourceQuoteItem::class);
    }
    
    /**
     * Add customer info to quote item, we need
     *
     * @param  int $productId
     * @return $this
     */
    public function addCustomerInfo($productId)
    {
        $this->getSelect()
            ->where('main_table.product_id = ?', $productId)
            ->joinLeft(
                ['q' => 'quote'],
                'main_table.quote_id = q.entity_id',
                ['customer_id', 'customer_firstname', 'customer_lastname', 'customer_note']
            )
            ->joinLeft(
                ['order' => 'sales_order'],
                'main_table.quote_id = order.quote_id',
                ['entity_id AS order_id', 'status as order_status']
            )
            ->joinLeft(
                ['order_item' => 'sales_order_item'],
                'main_table.item_id = order_item.quote_item_id',
                ['item_id AS order_item_id']
            );

        return $this;
    }

    /**
     * Filter by customer id and product id
     *
     * @param int $customerId
     * @param  int $productId
     * @return $this
     */
    public function filterCustomerProduct($customerId, $productId)
    {
        $this->getSelect()
            ->where('main_table.product_id = ?', $productId)
            ->joinLeft(
                ['q' => 'quote'],
                'main_table.quote_id = q.entity_id',
                ['customer_id']
            );

        $this->addFieldToFilter('q.customer_id', $customerId);

        return $this;
    }
}
