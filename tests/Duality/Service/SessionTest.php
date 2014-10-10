<?php

class SessionTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test session service
     */
    public function testSession()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('session');
    }
}