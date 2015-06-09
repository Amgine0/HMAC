<?php namespace Amgine0\HMAC;

/*
 * FactoryTest.php
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

class FactoryTest extends \PHPUnit_Framework_TestCase {

    protected $factory;

    public function setUp() {
        $this->factory = new Factory;
    }

    public function testClass() {
        $this->assertTrue(
            method_exists( 'Amgine0\HMAC\Factory', 'getToken' ),
            'testClass:01: Class missing method getToken.'
        );
        $this->assertTrue(
            method_exists( 'Amgine0\HMAC\Factory', 'getRequest' ),
            'testClass:02: Class missing method getRequest.'
        );
    }

    public function testInstantiation() {
        $this->assertInstanceOf(
            'Amgine0\HMAC\Factory',
            new Factory,
            'testInstantiation:01: Wrong class name.' );
    }

    public function testGetToken() {
        $this->assertInstanceOf(
            'Amgine0\HMAC\Model\Token',
            $this->factory->getToken( 'key', 'secret' ),
            'testGetToken:01: Does not return correct class instance.'
        );
    }

    public function testGetRequest() {
        $this->assertInstanceOf(
            'Amgine0\HMAC\Model\Request',
            $this->factory->getRequest( 'GET', 'v0/endpoint', array( 'a' => 1, 'b' => 'string', 'c' => array( 1, 'string' ) ) ),
            'testGetRequest:01: Does not return correct class instance.'
        );
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     **/
    public function testBadGetToken () {
        $this->factory->getRequest( 'key', array( 1, 2, 'a' ) );
    }
}
