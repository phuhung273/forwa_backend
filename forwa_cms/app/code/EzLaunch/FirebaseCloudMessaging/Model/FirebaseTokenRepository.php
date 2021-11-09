<?php

/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Model;

use EzLaunch\FirebaseCloudMessaging\Api\Data\FirebaseTokenInterface;
use EzLaunch\FirebaseCloudMessaging\Api\FirebaseTokenRepositoryInterface;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\TemporaryState\CouldNotSaveException as TemporaryCouldNotSaveException;
use Magento\Framework\DB\Adapter\ConnectionException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Psr\Log\LoggerInterface;

class FirebaseTokenRepository implements FirebaseTokenRepositoryInterface 
{
    /**
     * @var \EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken
     */
    private $resourceModel;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var FirebaseTokenFactory
     */
    private $tokenFactory;

    /**
     * @param \EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken $resourceModel
     * @param LoggerInterface $logger
     * @param FirebaseTokenFactory $tokenFactory
     */
    public function __construct(
        \EzLaunch\FirebaseCloudMessaging\Model\ResourceModel\FirebaseToken $resourceModel,
        LoggerInterface $logger,
        FirebaseTokenFactory $tokenFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->logger = $logger;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * @inheritdoc
     */
    public function getByCustomerIdAndDeviceName($customerId, $deviceName){
        $token = $this->tokenFactory->create()->loadByCustomerIdAndDeviceName($customerId, $deviceName);
        if (!$token->getValue()) {
            // token does not exist
            throw new NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue',
                    [
                        'fieldName' => 'customerId',
                        'fieldValue' => $customerId,
                    ]
                )
            );
        } else {
            return $token;
        }
    }

    /**
     * @inheritdoc
     */
    public function save($token, $customerId){
        try {
            // TODO: Multiple device
            $tokenModel = $this->getByCustomerIdAndDeviceName($customerId, $token->getDeviceName());
            $tokenModel->setValue($token->getValue());
            $tokenModel->setDeviceName($token->getDeviceName());
            $this->saveToken($tokenModel);
        } catch (NoSuchEntityException $e) {
            $token->setCustomerId($customerId);
            $this->saveToken($token);
        }

        return $token->getValue();
    }

    /**
     * Save token resource model.
     *
     * @param FirebaseTokenInterface|FirebaseToken $token
     * @throws TemporaryCouldNotSaveException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function saveToken($token): void
    {
        try {
            $this->resourceModel->save($token);
        } catch (ConnectionException $exception) {
            throw new TemporaryCouldNotSaveException(
                __('Database connection error'),
                $exception,
                $exception->getCode()
            );
        } catch (DeadlockException $exception) {
            throw new TemporaryCouldNotSaveException(
                __('Database deadlock found when trying to get lock'),
                $exception,
                $exception->getCode()
            );
        } catch (LockWaitException $exception) {
            throw new TemporaryCouldNotSaveException(
                __('Database lock wait timeout exceeded'),
                $exception,
                $exception->getCode()
            );
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('The token was unable to be saved. Please try again.'),
                $e
            );
        }
    }

    public function delete($customerId, $deviceName){
        $token = $this->getByCustomerIdAndDeviceName($customerId, $deviceName);
        $id = $token->getId();
        try {
            $this->resourceModel->delete($token);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new StateException(
                __('The token product couldn\'t be removed.'),
                $e
            );
        }

        return $id;
    }
}