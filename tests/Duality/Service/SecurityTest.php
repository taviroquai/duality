<?php

class SecurityTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test security service
     */
    public function testSecurity()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('security');
    }
}