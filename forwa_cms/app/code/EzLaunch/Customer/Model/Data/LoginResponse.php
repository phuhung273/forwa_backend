<?php

namespace EzLaunch\Customer\Model\Data;

use EzLaunch\Customer\Api\Data\LoginResponseInterface;
use Magento\Framework\Model\AbstractModel;

class LoginResponse extends AbstractModel implements LoginResponseInterface {

    /**
     * @inheritdoc
     */
    public function getAccessToken(){
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken($token){
        return $this->setData(self::ACCESS_TOKEN, $token);
    }

    /**
     * @inheritdoc
     */
    public function setStoreCode($code){
        return $this->setData(self::STORE_CODE, $code);
    }

    /**
     * @inheritdoc
     */
    public function getStoreCode(){
        return $this->getData(self::STORE_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setStoreWebsiteId($id){
        return $this->setData(self::WEBSITE_ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getStoreWebsiteId(){
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @inheritdoc
     */
    public function getCustomer(){
        return $this->getData(self::CUSTOMER);
    }

    /**
     * @inheritdoc
     */
    public function setCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer){
        return $this->setData(self::CUSTOMER, $customer);
    }
}