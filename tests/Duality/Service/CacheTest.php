<?php

class CacheTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test cache service
     */
    public function testCache()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $cache = $app->call('cache');

        $cache->add('dummy', 'dummy');
        $cache->put('dummy', 'dummy');
        $cache->asArray();
        $cache->pull('dummy');
        $cache->importArray(array('dummy', 'dummy'));
        $cache->reset();
        $cache->terminate();
    }
}