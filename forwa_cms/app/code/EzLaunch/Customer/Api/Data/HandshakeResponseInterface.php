<?php
/**
 * Copyright © EzLaunch, Inc. All rights reserved.
 */

namespace EzLaunch\Customer\Api\Data;

/**
 * Handshake entity interface for API handling.
 *
 * @api
 * @since 100.0.2
 */


interface HandshakeResponseInterface {
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const KEY_ACCESS_TOKEN = 'access_token';
    /**#@-*/

    /**
     * Get token.
     *
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set token.
     *
     * @param string $token
     * @return $this
     */
    public function setAccessToken(string $token);
}