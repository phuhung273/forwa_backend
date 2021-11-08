<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Model\ResourceModel;


class FirebaseToken extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'customer_firebase_token';
    const ID_FIELD_NAME = 'entity_id';
	
	protected function _construct()
	{
		$this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
	}
	
	/**
     * Load by customer id
     *
     * @param \EzLaunch\FirebaseCloudMessaging\Model\FirebaseToken $token
     * @param int $customerId
     * @param string $deviceName
     * @return $this
     */
    public function loadByCustomerIdAndDeviceName(\EzLaunch\FirebaseCloudMessaging\Model\FirebaseToken $token, $customerId, $deviceName)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
				$this->getMainTable(),
				$this->getIdFieldName()
			)
			->where('customer_id = ?', $customerId)
			->where('device_name = ?', $deviceName);

        $tokenId = $connection->fetchOne($select);
        if ($tokenId) {
            $this->load($token, $tokenId);
        } else {
            $token->setData([]);
        }

        return $this;
    }
}