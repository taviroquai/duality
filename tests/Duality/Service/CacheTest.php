<?php

class CacheTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test cache service
     * @requires extension apc
     */
    public function testCache()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $cache = $app->call('cache');

        $expected = 'dummy';
        $cache->add('dummy', $expected);
        $result = $cache->get('dummy');
        $this->assertEquals($expected, $result);
        
        $expected = 'dummy';
        $cache->put('dummy', $expected);
        $result = $cache->get('dummy');
        $this->assertEquals($expected, $result);

        $expected = array();
        $cache->remove('dummy');
        $result = $cache->asArray();
        $this->assertEquals($expected, $result);

        $expected1 = 'dummy';
        $expected2 = array();
        $cache->put('dummy', $expected1);
        $result1 = $cache->pull('dummy');
        $result2 = $cache->asArray();
        $this->assertEquals($expected1, $result1);
        $this->assertEquals($expected2, $result2);

        $expected = array();
        $cache->reset();
        $result = $cache->asArray();
        $this->assertEquals($expected, $result);

        $expected = array('dummy' => 'dummy');
        $cache->importArray($expected);
        $result = $cache->asArray();
        $this->assertEquals($expected, $result);

        
        
        $cache->terminate();
    }
}