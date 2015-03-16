<?php

class SessionTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test session service
     */
    public function testSession()
    {
        $app = new \Duality\App();
        $session = $app->call('session');

        $this->assertInstanceOf('\Duality\Service\Session\Dummy', $session);
    }
}