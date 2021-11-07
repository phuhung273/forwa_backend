<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\FirebaseCloudMessaging\Api;

/**
 * Firebase Cloud Messaging Interface
 *
 */
interface FcmServiceInterface
{
    /**
     * Send notification to device with token
     *
     * @param string $token
     * @param string $title
     * @param string $body
     */
    public function send(string $token, string $title, string $body);
}
