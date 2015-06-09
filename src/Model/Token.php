<?php namespace Amgine0\HMAC\Model;

/*
 * TokenModel.php
 *
 * Copyright 2015 Amgine <amgine@saewyc.ca>
 *
 * This program is free software. It comes without any warranty, to the extent
 * permitted by applicable law. You can redistribute it and/or modify it under
 * the terms of the Do What The Fuck You Want To Public License, Version 2, as
 * published by Sam Hocevar. See http://www.wtfpl.net/ for more details.
 *
 *
 */

/**
 * class: Token
 **
 * A Value Object to hold the key and secret for a user's account, for authentication of requests.
 **/
class Token {
    /**
     * Key used to identify user.
     **
     * @var string $key
     **/
    private $key = null;
    /**
     * Secret used to hash request for HMAC authentication.
     **
     * @var string $secret
     **/
    private $secret = null;

    /**
     * method: __construct
     **
     * Object constructor assigns passed values to object properties.
     **
     * @parameter $key string User identifying key.
     * @parameter $secret string User's salt for hashing.
     * @return void
     **/
    public function __construct( $key, $secret ) {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * method: getKey
     **
     * Checks property is set and returns value or bool false if not set.
     **
     * @return mixed/string OR bool FALSE
     **/
    public function getKey() {
        if( isset( $this->key ) ) {
            return $this->key;
        }
        return false;
    }

    /**
     * method: getSecret
     **
     * Checks property is set and returns value or bool false if not set.
     **
     * @return mixed/string OR bool FALSE
     **/
    public function getSecret() {
        if( isset( $this->secret ) ) {
            return $this->secret;
        }
        return false;
    }
}
