<?php namespace Amgine0\HMAC;

/*
 * Factory.php
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
 * class: Factory
 **
 * Generates objects for implementing HMAC.
 **/
class Factory {

    /**
     * method: getToken
     **
     * Returns instance of Amgine0\HMAC\Token.
     **
     * @param string    $key    User identifying key.
     * @param string    $secret User's secret.
     * @return object   Amgine0\HMAC\Model\Token instance
     **/
    public function getToken( $key, $secret ) {
        return new Model\Token( $key, $secret );
    }

    /**
     * method: getRequest
     **
     * Returns instance of Amgine0\HMAC\Model\Request
     **
     * @param string    $method HTTP method
     * @param string    $path   url path
     * @param array     $params Parameters used
     * @return object   Amgine0\HMAC\Model\Request instance
     **/
    public function getRequest( $method, $path, $params ) {
        return new Model\Request( $method, $path, $params );
    }
}
