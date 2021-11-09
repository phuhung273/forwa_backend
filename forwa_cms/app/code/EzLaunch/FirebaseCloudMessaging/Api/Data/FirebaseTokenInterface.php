<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */
namespace EzLaunch\FirebaseCloudMessaging\Api\Data;

/**
 * Store interface
 *
 * @api
 * @since 100.0.2
 */
interface FirebaseTokenInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const KEY_ID = 'entity_id';
    const KEY_CUSTOMER_ID = 'customer_id';
    const KEY_VALUE = 'value';
    const KEY_DEVICE_NAME = 'device_name';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * Retrieve token
     *
     * @return string
     */
    public function getValue();

    /**
     * Set token
     *
     * @param string $token
     * @return $this
     */
    public function setValue($token);

    /**
     * Retrieve device name
     *
     * @return string
     */
    public function getDeviceName();

    /**
     * Set device name
     *
     * @param string $deviceName
     * @return $this
     */
    public function setDeviceName($deviceName);

    /**
     * Retrieve customer id
     * 
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);
}
