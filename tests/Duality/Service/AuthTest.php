<?php

class AuthTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service
     */
    public function testAuth()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('auth');
    }
}