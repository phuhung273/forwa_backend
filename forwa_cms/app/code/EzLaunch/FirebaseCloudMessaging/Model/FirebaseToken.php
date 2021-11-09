<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Model;

use EzLaunch\FirebaseCloudMessaging\Api\Data\FirebaseTokenInterface;

class FirebaseToken extends \Magento\Framework\Model\AbstractModel implements FirebaseTokenInterface
{    
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\FirebaseToken::class);
    }

    /**
     * Load by customer id
     *
     * @param int $customerId
     * @param string $deviceName
     * @return $this
     */
    public function loadByCustomerIdAndDeviceName($customerId, $deviceName)
    {
        $this->_getResource()->loadByCustomerIdAndDeviceName($this, $customerId, $deviceName);
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->_getData(seLF::KEY_ID);
    }

    /**
     * @inheritdoc
     */
    public function getValue() {
        return $this->_getData(seLF::KEY_VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setValue($token) {
        return $this->setData(seLF::KEY_VALUE, $token);
    }

    /**
     * @inheritdoc
     */
    public function getDeviceName() {
        return $this->_getData(seLF::KEY_DEVICE_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setDeviceName($deviceName) {
        return $this->setData(seLF::KEY_DEVICE_NAME, $deviceName);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId() {
        return $this->_getData(seLF::KEY_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId) {
        return $this->setData(seLF::KEY_CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerFirstName()
    {
        return $this->getData(self::KEY_CUSTOMER_FIRSTNAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerFirstName($name)
    {
        return $this->setData(self::KEY_CUSTOMER_FIRSTNAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerLastName()
    {
        return $this->getData(self::KEY_CUSTOMER_LASTNAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerLastName($name)
    {
        return $this->setData(self::KEY_CUSTOMER_LASTNAME, $name);
    }
}