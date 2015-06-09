<?php
use Amgine0\HMAC\Model;

/*
 * TokenTest.php
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

class TokenTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation() {
        $testToken = new Amgine0\HMAC\Model\Token( 'key', 'secret' );
        $this->assertInstanceOf(
            'Amgine0\HMAC\Model\Token',
            $testToken,
            'testInstantiation:01: Wrong class name.'
        );
        $this->assertEquals(
            'key',
            $testToken->getKey(),
            'testInstantiation:02: getKey retrieves wrong value.'
        );
        $this->assertEquals(
            'secret',
            $testToken->getSecret(),
            'testInstantiation:03: getSecret retrieves wrong value.'
        );
    }
}
