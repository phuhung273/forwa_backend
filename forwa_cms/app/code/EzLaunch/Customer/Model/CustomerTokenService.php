<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use EzLaunch\Customer\Api\CustomerTokenServiceInterface;
use EzLaunch\FirebaseCloudMessaging\Api\FirebaseTokenRepositoryInterface;
use Magento\Authorization\Model\CompositeUserContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @inheritdoc
 */
class CustomerTokenService implements CustomerTokenServiceInterface
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

    /**
     * @var Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Customer Account Service
     *
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var \Magento\Integration\Model\CredentialsValidator
     */
    private $validatorHelper;

    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    private $tokenModelCollectionFactory;

    /**
     * @var Data\LoginResponseFactory
     */
    protected $_loginResponseFactory;

    /**
     * @var RequestThrottler
     */
    private $requestThrottler;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CompositeUserContext
     */
    private $userContext;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \EzLaunch\Customer\Model\Data\HandshakeResponseFactory
     */
    private $handshakeResponseFactory;

    /**
     * @var FirebaseTokenRepositoryInterface
     */
    private $firebaseTokenRepository;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param AccountManagementInterface $accountManagement
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     * @param \Magento\Integration\Model\CredentialsValidator $validatorHelper
     * @param Data\LoginResponseFactory $loginResponseFactory
     * @param StoreManagerInterface $storeManager
     * @param CompositeUserContext $userContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param \EzLaunch\Customer\Model\Data\HandshakeResponseFactory $handshakeResponseFactory
     * @param FirebaseTokenRepositoryInterface $firebaseTokenRepository
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        AccountManagementInterface $accountManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper,
        \EzLaunch\Customer\Model\Data\LoginResponseFactory $loginResponseFactory,
        StoreManagerInterface $storeManager,
        CompositeUserContext $userContext,
        CustomerRepositoryInterface $customerRepository,
        \EzLaunch\Customer\Model\Data\HandshakeResponseFactory $handshakeResponseFactory,
        FirebaseTokenRepositoryInterface $firebaseTokenRepository,
        ManagerInterface $eventManager = null
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->accountManagement = $accountManagement;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
        $this->validatorHelper = $validatorHelper;
        $this->eventManager = $eventManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(ManagerInterface::class);

        $this->_loginResponseFactory = $loginResponseFactory;
        $this->storeManager = $storeManager;
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
        $this->handshakeResponseFactory = $handshakeResponseFactory;
        $this->firebaseTokenRepository = $firebaseTokenRepository;
    }

    /**
     * @inheritdoc
     */
    public function login($username, $password, $firebaseToken)
    {
        $this->validatorHelper->validate($username, $password);
        $this->getRequestThrottler()->throttle($username, RequestThrottler::USER_TYPE_CUSTOMER);
        try {
            $customerDataObject = $this->accountManagement->authenticate($username, $password);
        } catch (\Exception $e) {
            $this->getRequestThrottler()->logAuthenticationFailure($username, RequestThrottler::USER_TYPE_CUSTOMER);
            throw new AuthenticationException(
                __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                )
            );
        }
        $this->eventManager->dispatch('customer_login', ['customer' => $customerDataObject]);
        $this->getRequestThrottler()->resetAuthenticationFailuresCount($username, RequestThrottler::USER_TYPE_CUSTOMER);
        $token = $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();

        $this->firebaseTokenRepository->save($firebaseToken, $customerDataObject->getId());

        $loginResponse = $this->_loginResponseFactory->create();
        $loginResponse->setCustomer($customerDataObject);
        $loginResponse->setAccessToken($token);

        $store = $this->storeManager->getStore($customerDataObject->getStoreId());
        $loginResponse->setStoreCode($store->getCode());
        $loginResponse->setStoreWebsiteId($store->getWebsiteId());
        
        return $loginResponse;
    }

    /**
     * @inheritdoc
     */
    public function handshake()
    {
        $customerId = $this->userContext->getUserId();

        $handshakeResponse = $this->handshakeResponseFactory->create();
        
        if (!isset($customerId)) {
            return $handshakeResponse;
        }

        $customer = $this->customerRepository->getById($customerId);
        $token = $this->tokenModelFactory->create()->createCustomerToken($customer->getId())->getToken();

        $handshakeResponse->setAccessToken($token);
        
        return $handshakeResponse;
    }

    /**
     * Get request throttler instance
     *
     * @return RequestThrottler
     * @deprecated 100.0.4
     */
    private function getRequestThrottler()
    {
        if (!$this->requestThrottler instanceof RequestThrottler) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(RequestThrottler::class);
        }
        return $this->requestThrottler;
    }

    /**
     * @inheritdoc
     */
    public function logout($deviceName, $customerId)
    {
        $tokenId = $this->firebaseTokenRepository->delete($customerId, $deviceName);
        return $tokenId;
    }
}
