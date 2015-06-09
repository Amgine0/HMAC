<?php namespace Amgine0\HMAC\Model;

/*
 * RequestTest.php
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

class RequestTest extends \PHPUnit_Framework_TestCase {

    protected $HMACfactory;
    protected $obj;
    protected $HMACtoken;
    protected $HMACrequestSend;
    protected $params;

    public function setUp() {
        $this->params = array( 1, 'string', 'a' => 3.1415926, 'b' => array( 0, 'string' ) );
        $this->HMACfactory = new \Amgine0\HMAC\Factory;
        $this->HMACtoken = $this->HMACfactory->getToken( 'testKey', 'testSecret' );
        $this->HMACrequestSend = $this->HMACfactory->getRequest( 'GET', '/v0/endpoint', $this->params );
    }

    public function testClass() {
        $this->assertTrue(
            property_exists(
                $this->HMACrequestSend,
                'authenticationParameters'
            ),
            'testClass:01: Missing authenticationParameters property.'
        );
        $this->assertTrue(
            property_exists(
                $this->HMACrequestSend,
                'method'
            ),
            'testClass:02: Missing HTTP method property.'
        );
        $this->assertTrue(
            property_exists(
                $this->HMACrequestSend,
                'queryParameters'
            ),
            'testClass:03: Missing queryParameters property.'
        );
        $this->assertTrue(
            property_exists(
                $this->HMACrequestSend,
                'parameters'
            ),
            'testClass:04: Missing parameters property.'
        );
        $this->assertTrue(
            property_exists(
                $this->HMACrequestSend,
                'path'
            ),
            'testClass:05: Missing path property.'
        );
        $this->assertTrue(
            method_exists(
                $this->HMACrequestSend,
                'authenticate'
            ),
            'testClass:05: Missing authenticate method.'
        );
        $this->assertTrue(
            method_exists(
                $this->HMACrequestSend,
                'sign'
            ),
            'testClass:05: Missing authenticate method.'
        );

    }

    public function testInstantiation() {
        $this->assertInstanceOf(
            'Amgine0\HMAC\Model\Request',
            new Request( 'GET', '/v0/endpoint', array( 1, 'string', 'a' => 3.1415926, 'b' => array( 0, 'string' ) ) ),
            'testInstantiation:01: Wrong class name.' );
    }

    /**
     * Test if authentication works.
     **/
    public function testSign() {
        $request = $this->HMACfactory->getRequest(
            'GET',
            '/v0/endpoint',
            array_merge( $this->params, $this->HMACrequestSend->sign( $this->HMACtoken ) )
        );
        $this->assertTrue(
            $request->authenticate( $this->HMACtoken ),
            'testSign:01: Fails to authenticate.'
        );
    }

    /**
     * Test incorrect auth_key throws correct exception.
     * @expectedException Amgine0\HMAC\AuthenticationException
     * @expectedExceptionMessage The auth_key is incorrect.
     **/
    public function testAuthenticationRequestWrongKey() {
        $token = $this->HMACfactory->getToken( 'not_correct_key', 'testSecret' );

        $request = $this->HMACfactory->getRequest(
            'GET',
            '/v0/endpoint',
            array_merge( $this->params, $this->HMACrequestSend->sign( $this->HMACtoken ) )
        );

        // Attempt to authenticate
        $request->authenticate( $token );
    }

    /**
     * Test missing token secret throws correct exception.
     * @expectedException Amgine0\HMAC\AuthenticationException
     * @expectedExceptionMessage Token secret not set.
     **/
    public function testAuthenticationRequestWrongSecret() {
        $token =  $this->HMACfactory->getToken( 'testKey', null );

        $request = $this->HMACfactory->getRequest(
            'GET',
            '/v0/endpoint',
            array_merge( $this->params, $this->HMACrequestSend->sign( $this->HMACtoken ) )
        );

        // Attempt to authenticate
        $request->authenticate( $token );
    }

    /**
     * Test incorrect auth_version throws correct exception.
     * @expectedException Amgine0\HMAC\AuthenticationException
     * @expectedExceptionMessage The auth_version is incompatible.
     **/
    public function testAuthenticationRequestWrongVersion() {
        $params = array_merge( $this->params, $this->HMACrequestSend->sign( $this->HMACtoken ) );
        $params['auth_version'] = '0.1b.0.0';

        $request = $this->HMACfactory->getRequest(
            'GET',
            '/v0/endpoint',
            $params
        );

        // Attempt to authenticate
        $request->authenticate( $this->HMACtoken );
    }

    /**
     * Test invalid auth_timestamp throws correct exception.
     * @expectedException Amgine0\HMAC\AuthenticationException
     * @expectedExceptionMessage The timestamp is invalid.
     **/
    public function testAuthenticationRequestInvalidTimestamp() {
        $params = array_merge( $this->params, $this->HMACrequestSend->sign( $this->HMACtoken ) );
        $params['auth_timestamp'] = time() + ( 60 * 60 * 24 );

        $request = $this->HMACfactory->getRequest(
            'GET',
            '/v0/endpoint',
            $params
        );

        // Attempt to authenticate
        $request->authenticate( $this->HMACtoken );
    }

    /**
     * Test incorrect auth_hash throws correct exception.
     * @expectedException Amgine0\HMAC\AuthenticationException
     * @expectedExceptionMessage The auth_hash is incorrect.
     **/
    public function testAuthenticationRequestIncorrectHash() {
        $params = array_merge( $this->params, $this->HMACrequestSend->sign( $this->HMACtoken ) );
        $params['auth_hash'] = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do';

        $request = $this->HMACfactory->getRequest(
            'GET',
            '/v0/endpoint',
            $params
        );

        // Attempt to authenticate
        $request->authenticate( $this->HMACtoken );
    }
}
