<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Model\Data;

use EzLaunch\Customer\Api\Data\HandshakeResponseInterface;
use Magento\Framework\Model\AbstractModel;

class HandshakeResponse extends AbstractModel implements HandshakeResponseInterface {

    /**
     * @inheritdoc
     */
    public function getAccessToken(){
        return $this->getData(self::KEY_ACCESS_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken(string $token){
        return $this->setData(self::KEY_ACCESS_TOKEN, $token);
    }
}