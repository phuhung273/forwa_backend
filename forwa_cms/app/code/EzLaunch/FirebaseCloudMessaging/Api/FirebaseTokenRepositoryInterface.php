<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Api;

use EzLaunch\FirebaseCloudMessaging\Api\Data\FirebaseTokenInterface;

interface FirebaseTokenRepositoryInterface {
    
    /**
     * Save token
     *
     * @param FirebaseTokenInterface $token
     * @param int $customerId
     * @return string
     * @throws TemporaryCouldNotSaveException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save($token, $customerId);

    /**
     * Get firebase token by customer id
     *
     * @param int $customerId
     * @param string $deviceName
     * @return \EzLaunch\FirebaseCloudMessaging\Model\FirebaseToken
     * @throws NoSuchEntityException
     */
    public function getByCustomerIdAndDeviceName($customerId, $deviceName);
}