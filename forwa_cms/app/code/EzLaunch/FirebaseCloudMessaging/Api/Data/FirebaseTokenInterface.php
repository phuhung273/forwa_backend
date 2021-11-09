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

    const KEY_CUSTOMER_FIRSTNAME = 'customer_firstname';
    const KEY_CUSTOMER_LASTNAME = 'customer_lastname';
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

    /**
     * Returns the Customer first name.
     *
     * @return string|null Customer first name. Otherwise, null.
     */
    public function getCustomerFirstName();

    /**
     * Sets the Customer first name.
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerFirstName($name);

    /**
     * Returns the Customer last name.
     *
     * @return string|null Customer last name. Otherwise, null.
     */
    public function getCustomerLastName();

    /**
     * Sets the Customer last name.
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerLastName($name);
}
