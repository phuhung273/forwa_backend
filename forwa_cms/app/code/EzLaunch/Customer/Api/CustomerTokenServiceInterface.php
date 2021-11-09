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
     * @param \EzLaunch\FirebaseCloudMessaging\Api\Data\FirebaseTokenInterface $firebaseToken
     * @return Data\LoginResponseInterface
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    public function login($username, $password, $firebaseToken);

    /**
     * Handshake
     * 
     * @return Data\HandshakeResponseInterface
     */
    public function handshake();

    /**
     * Logout, remove firebase token of device
     * 
     * @param string $deviceName
     * @param int $customerId
     * @return int
     */
    public function logout($deviceName, $customerId);
}
