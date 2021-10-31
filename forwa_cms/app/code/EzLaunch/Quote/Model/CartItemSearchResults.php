<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
declare(strict_types=1);

namespace EzLaunch\Quote\Model;

use EzLaunch\Quote\Api\Data\CartItemSearchResultsInterface;

use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with cart search results.
 */
class CartItemSearchResults extends SearchResults implements CartItemSearchResultsInterface
{

}
