<?php

class ClientTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test HTTP client service
     */
    public function testClient()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('client');
    }
}