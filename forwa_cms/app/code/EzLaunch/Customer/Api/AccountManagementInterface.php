<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Api;


interface AccountManagementInterface
{
    /**#@+
     * Constant for confirmation status
     */
    const ACCOUNT_CONFIRMED = 'account_confirmed';
    const ACCOUNT_CONFIRMATION_REQUIRED = 'account_confirmation_required';
    const ACCOUNT_CONFIRMATION_NOT_REQUIRED = 'account_confirmation_not_required';
    const MAX_PASSWORD_LENGTH = 256;
    /**#@-*/
    
    /**
     * Create customer + store. Perform necessary business operations like sending email.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $password
     * @param string $redirectUrl
     * @param bool $requireConfirmation
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function register(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $password = null,
        $redirectUrl = '', 
        $requireConfirmation = true
    );

    /**
     * Login or register an account with email after verified by social platform. Send greeting email
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function socialLogin(
        \Magento\Customer\Api\Data\CustomerInterface $customer
    );
}
