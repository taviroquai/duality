<?php

class CacheTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test session service
     */
    public function testCache()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('cache');
    }
}