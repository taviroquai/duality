<?php

class StorageTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test storage structure
     */
    public function testStorage()
    {
        $storage = new \Duality\Structure\Storage();
        $storage->insert(0, 'dummy');
    }
}