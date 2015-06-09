<?php namespace Amgine0\HMAC\Model;

use Amgine0\HMAC\AuthenticationException;

/*
 * Request.php
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
 * class: Request
 **
 * Value object of the request, including calculated hash.
 **/
class Request {
    /**
     * Version constant, to ensure encoding/decoding are compatible.
     *
     * NOTE: for this project the first two version numbers are used to determine encoding/hashing compatibility:
     *  /major/./minor/./stability/./build or release/
     * Stability indicates
     *  0           alpha, unstable
     *  1           beta, unstable
     *  2           rc, mostly stable
     *  3           stable release
     **/
    const VERSION = '0.0.0.0';

    /**
     * Array of authentication parameters.
     **
     * @var array $authenticationParameters
     **/
    public $authenticationParameters = array(
        'auth_version' => null,
        'auth_key' => null,
        'auth_timestamp' => null,
        'auth_hash' => null
    );
    /**
     * HTTP method (verb)
     **
     * @var string $method
     **/
    protected $method = false;
    /**
     * Query parameters
     **
     * @var array queryParameters
     **/
    protected $queryParameters = array();
    /**
     * The data being sent/received.
     **
     * @var array $parameters
     **/
    protected $parameters = array();
    /**
     * The API path, including endpoint.
     **
     * @var string $path
     **/
    protected $path = false;

    /**
     * method: __construct
     **
     * Set initial instance properties.
     **
     * @param string    $method     HTTP method
     * @param string    $path       API path
     * @param array     $parameters Data to be sent/received
     * @return void
     **/
    public function __construct( $method, $path, array $parameters ) {
        $this->method = strtoupper( $method );
        $this->path = $path;

        foreach( $parameters as $k => $v ) {
            $k = strtolower( $k );
            substr( $k, 0, 5 ) == 'auth_' ? $this->authenticationParameters[$k] = $v : $this->queryParameters[$k] = $v;
        }
    }

    /**
     * method: authenticate
     **
     * Authenticate a data payload
     **
     * @param object    $token  Amgine0\HMAC\Model\Token
     * @param int       $timestampGrace Optional grace period for mismatched clocks
     * @return bool (from $this->authenticateByToken() )
     **/
    public function authenticate( Token $token, $timestampGrace = 600 ) {
        if ( $this->authenticationParameters['auth_key'] == $token->getKey() ) {
            return $this->authenticateByToken( $token, $timestampGrace );
        }

        throw new AuthenticationException( 'The auth_key is incorrect.' );
    }

    /**
     * method: authenticateByToken
     **
     * Validate authentication parameters and hash
     **
     * @param object    $token  Amgine0\HMAC\Model\Token
     * @param int       $timestampGrace Milliseconds of grace period
     * @return bool TRUE or void
     **/
    protected function authenticateByToken( Token $token, $timestampGrace ) {
        if ( $token->getSecret() != null ) {
            $this->validateVersion();
            $this->validateTimestamp( $timestampGrace );
            $this->validateHash( $token );
            return true;
        }

        throw new AuthenticationException( 'Token secret not set.' );
    }

    /**
     * method: encode
     **
     * Return the hashed data payload.
     **
     * @param object    $token  Amgine0\HMAC\Model\Token
     * @return string
     **/
    protected function encode( Token $token ) {
        return hash_hmac( 'sha512', $this->getDataString(), $token->getSecret() );
    }

    /**
     * method: getDataString
     **
     * Return data key=value as \n imploded string.
     **
     * @return string
     **/
    protected function getDataString() {
        return implode( "\n", array( $this->method, $this->path, $this->getParamsAsString() ) );
    }

    /**
     * method: getParamsAsString
     **
     * Return parameters as http query string.
     **
     * @return string
     **/
    protected function getParamsAsString() {
        $tmp = array();
        $params = array_merge( $this->authenticationParameters, $this->queryParameters );

        foreach( $params as $key => $value ) {
            $tmp[strtolower( $key )] = $value;
        }

        // ensure hash value is not set.
        unset( $tmp['auth_hash'] );

        return http_build_query( $tmp );
    }

    /**
     * method: sign
     **
     * Set authentication parameters, encode the data, and return signed payload.
     **
     * @param object    $token  Amgine0\HMAC\Model\Token
     * @return array    $this->authenticationParameters
     **/
    public function sign( Token $token ) {
        $this->authenticationParameters = array(
            'auth_version' => Request::VERSION,
            'auth_key' => $token->getKey(),
            'auth_timestamp' => time()
        );
        $this->authenticationParameters['auth_hash'] = $this->encode( $token );

        return $this->authenticationParameters;
    }

    /**
     * method: validateHash
     **
     * Check if the data encodes to the same value.
     **
     * @param object    $token  Amgine0\HMAC\Model\Token
     * @return bool TRUE
     **/
    protected function validateHash( Token $token ) {
        if ( $this->authenticationParameters['auth_hash'] === $this->encode( $token ) ) {
            return true;
        }

        throw new AuthenticationException( 'The auth_hash is incorrect.' );
    }

    /**
     * method: validateTimestamp
     **
     * Check if the timestamp is recent enough to consider processing.
     *
     * NB: Windows and other computers may not be able to agree on the same UTC time.
     * NB: If grace period is set to zero, this check is ignored.
     **
     * @param int   $timestampGrace The acceptable margin of time difference, set in $this->autheticate()
     * @return bool TRUE
     **/
    protected function validateTimestamp( $timestampGrace ) {
        if ( $timestampGrace == 0 ) {
            return true;
        }
        if ( !isset( $this->authenticationParameters['auth_timestamp'] ) ) {
            throw new AuthenticationException( 'The timestamp is not set.' );
        }

        $difference = abs( $this->authenticationParameters['auth_timestamp'] - time() );
        if ( $difference > $timestampGrace ) {
            throw new AuthenticationException( 'The timestamp is invalid.' );
        }
    }

    /**
     * method: validateVersion
     **
     * Check the validation version is compatible.
     **
     * @return bool TRUE
     **/
    protected function validateVersion() {
        $current = explode( '.', Request::VERSION );
        $auth = explode( '.', $this->authenticationParameters['auth_version'] );

        if ( $current[0] == $auth[0] && $current[1] == $auth[1] ) {
            return true;
        }

        throw new AuthenticationException( 'The auth_version is incompatible.' );
    }

}
