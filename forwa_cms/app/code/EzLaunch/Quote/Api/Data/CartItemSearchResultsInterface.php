<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
namespace EzLaunch\Quote\Api\Data;

/**
 * Cart item search result interface.
 */
interface CartItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \EzLaunch\Quote\Api\Data\CartItemInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \EzLaunch\Quote\Api\Data\CartItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
