<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Api;

/**
 * Interface providing token generation for Customers
 *
 * @api
 * @since 100.0.2
 */
interface CustomerTokenServiceInterface
{
    /**
     * Login and return accessToken, customer
     *
     * @param string $username
     * @param string $password
     * @return Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function login($username, $password);

    /**
     * Handshake
     * 
     * @return Data\HandshakeResponseInterface
     */
    public function handshake();
}
