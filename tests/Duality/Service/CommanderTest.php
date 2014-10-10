<?php

class CommanderTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test HTTP client service
     */
    public function testCommander()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('cmd');
    }
}