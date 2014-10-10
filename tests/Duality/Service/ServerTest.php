<?php

class ServerTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test server service
     */
    public function testServer()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('server');
    }
}