<?php

use Duality\Structure\Property;
use Duality\Structure\Entity;

class Model extends Entity
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

        $name = 'dummy';
        $expected = array(
            new Property('id'),
            new Property($name)
        );
        $entity->addPropertiesFromArray(array($name));
        $result = $entity->getProperties();
        $this->assertEquals($expected, $result);
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