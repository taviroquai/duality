<?php

class Model
extends \Duality\Structure\Entity
{
}

class EntityTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test entity structure
     */
    public function testEntity()
    {
        $entity = new \Model();
        $entity->addPropertiesFromArray(array('dummy'));
    }

    /**
     * Test invalid properties array
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidPropertiesArray()
    {
        $entity = new \Model();
        $entity->addPropertiesFromArray('dummy');
    }
}