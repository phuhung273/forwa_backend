<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Model;

use EzLaunch\Core\Helper\PathHelper;
use EzLaunch\FirebaseCloudMessaging\Api\FcmServiceInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Psr\Log\LoggerInterface;

/**
 * Firebase Cloud Messaging service
 *
 */
class FcmService implements FcmServiceInterface
{
    const FIREBASE_CREDENTIAL_FILENAME = 'forwa-90ef3-firebase-adminsdk-25c5j-b409e38529.json';

    /**
     * @var Messaging
     */
    private $messaging;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor.
     * @param \Kreait\Firebase\Factory $fcmFactory
     * @param DirectoryList $dir
     * @param PathHelper $pathHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Kreait\Firebase\Factory $fcmFactory,
        DirectoryList $dir,
        PathHelper $pathHelper,
        LoggerInterface $logger
    ){
        $this->logger = $logger;
        $firebaseCredentialFilePath = $pathHelper->join_paths($dir->getRoot(), self::FIREBASE_CREDENTIAL_FILENAME);
        $this->messaging = $fcmFactory->withServiceAccount($firebaseCredentialFilePath)->createMessaging();
    }

    /**
     * @inheritdoc
     */
    public function send($token, $title = null, $body){
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));
        
        try {
            $this->messaging->send($message);
        } catch (MessagingException $e){
            $this->logger->error($e->getMessage());
        } catch (FirebaseException $e){
            $this->logger->error($e->getMessage());
        }
    }
}
