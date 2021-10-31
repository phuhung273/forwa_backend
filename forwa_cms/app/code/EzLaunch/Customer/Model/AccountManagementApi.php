<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Model;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Account Management service implementation for external API access.
 * Handle various customer account actions.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AccountManagementApi extends AccountManagement
{
    /**
     * @inheritDoc
     *
     * Override register method to unset confirmation attribute for security purposes.
     */
    public function register(CustomerInterface $customer, $password = null, $redirectUrl = '', $requireConfirmation = true)
    {
        $customer = parent::register($customer, $password, $redirectUrl, $requireConfirmation);
        $customer->setConfirmation(null);

        return $customer;
    }
}
