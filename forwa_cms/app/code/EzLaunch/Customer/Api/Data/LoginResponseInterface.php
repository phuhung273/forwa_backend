<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Api\Data;

/**
 * Login entity interface for API handling.
 *
 * @api
 * @since 100.0.2
 */


interface LoginResponseInterface {
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ACCESS_TOKEN = 'access_token';
    const CUSTOMER = 'customer';
    const STORE_CODE = 'store_code';
    const WEBSITE_ID = 'store_website_id';
    /**#@-*/

    /**
     * Get access token
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Set access token
     *
     * @param string $token
     * @return $this
     */
    public function setAccessToken($token);

    /**
     * Set store code
     *
     * @param string $code
     * @return $this
     */
    public function setStoreCode($code);

    /**
     * Get store code
     *
     * @return string
     */
    public function getStoreCode();

    /**
     * Set websiteId
     *
     * @param int $id
     * @return $this
     */
    public function setStoreWebsiteId($id);

    /**
     * Get websiteId
     *
     * @return int
     */
    public function getStoreWebsiteId();

    /**
     * Get customer.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer();

    /**
     * Set customer.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    public function setCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer);
}