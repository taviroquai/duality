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
        $session = $app->call('session');

        $this->assertInstanceOf('\Duality\Service\Session\Dummy', $session);
    }
}