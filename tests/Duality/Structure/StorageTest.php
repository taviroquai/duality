<?php

use Duality\Structure\Storage;

class StorageTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test storage structure
     */
    public function testStorage()
    {
        $storage = new Storage();
        $result = $storage->asArray();
        $this->assertEquals(array(), $result);

        $expected = 'dummy';
        $storage->insert(0, $expected);
        $result = $storage->get(0);
        $this->assertEquals($expected, $result);
    }
}