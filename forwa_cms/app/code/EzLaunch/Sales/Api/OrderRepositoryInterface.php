<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
namespace EzLaunch\Sales\Api;

/**
 * Order repository interface.
 *
 */
interface OrderRepositoryInterface
{
    /**
     * Lists orders that match specified search criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included. See https://devdocs.magento.com/codelinks/attributes.html#OrderRepositoryInterface to
     * determine which call to use to get detailed information about all attributes for an object.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria The search criteria.
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getCustomList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
