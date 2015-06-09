<?php use Amgine0\HMAC;

/*
 * AuthenticationExceptionTest.php
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

class AuthenticationExceptionTest extends PHPUnit_Framework_TestCase {

    public function testInstantiation() {
        $this->assertInstanceOf( 'Amgine0\HMAC\AuthenticationException', new Amgine0\HMAC\AuthenticationException );
    }

}
