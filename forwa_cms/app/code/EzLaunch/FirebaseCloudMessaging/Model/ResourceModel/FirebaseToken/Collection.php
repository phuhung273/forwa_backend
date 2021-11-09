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

    /**
     * Add customer info to firebase token
     *
     * @param  int $customerId
     * @return $this
     */
    public function addCustomerInfo(int $customerId)
    {
        $this->getSelect()
            ->where('main_table.customer_id = ?', $customerId)
            ->joinLeft(
                ['customer' => 'customer_entity'],
                'main_table.customer_id = customer.entity_id',
                ['firstname', 'lastname']
            );

        return $this;
    }
}