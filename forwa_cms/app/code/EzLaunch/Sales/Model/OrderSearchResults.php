<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace EzLaunch\Sales\Model;

use Magento\Framework\Api\SearchResults;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

/**
 * Service Data Object with Order search results.
 */
class OrderSearchResults extends SearchResults implements OrderSearchResultInterface
{
    /**
     * Set items.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null){
        if(!isset($items)){
            return $this;
        }

        return parent::setItems($items);
    }
}
