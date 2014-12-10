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
        $config = array(
            'db' => array(
                'dsn'   => DB_DSN,
                'user'  => DB_USER,
                'pass'  => DB_PASS
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $db = $app->call('db');
        
        $entity = new \Model($db);

        $name = 'dummy';
        $expected = array(
            new Property('id'),
            new Property($name)
        );
        $entity->addPropertiesFromArray(array($name));
        $result = $entity->getProperties();
        $this->assertEquals($expected, $result);
    }
}