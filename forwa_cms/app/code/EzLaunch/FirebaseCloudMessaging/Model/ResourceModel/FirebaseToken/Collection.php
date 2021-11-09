<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken;


use EzLaunch\FirebaseCloudMessaging\Model\FirebaseToken;
use EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken as FirebaseTokenResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(FirebaseToken::class, FirebaseTokenResourceModel::class);
    }
}