<?php

use Duality\Structure\Property;
use Duality\Structure\Entity\User;
use Duality\Service\Database\SQLite;
use Duality\Structure\Database\Table;
use Duality\Structure\Database\Filter;

class SQLiteTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test SQLite database service with invalid config
     * 
     * @requires extension pdo
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testSQLiteInvalidConfig()
    {
        $config = array(
            'db' => array()
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $db = $app->call('db');
    }

    /**
     * Test SQLite database service
     * 
     * @requires extension pdo
     */
    public function testSQLite()
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
    }

    /**
     * Test missing schema configuration
     * 
     * @requires extension pdo
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testCreateSchemaInvalidConfig()
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

        $db->getSchemaConfig();
    }

    /**
     * Test schema
     * 
     * @requires extension pdo
     */
    public function testCreateSchema()
    {
        $config = array(
            'db' => array(
                'dsn'   => DB_DSN,
                'user'  => DB_USER,
                'pass'  => DB_PASS,
                'schema'=> DB_SCHEMA
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $db = $app->call('db');

        $schema = array();
        $db->reloadFromConfig($schema);

        $schema = $db->getSchemaConfig();
        $db->reloadFromConfig($schema);
        $db->createFromConfig();
        $db->updateFromConfig();
        $db->seedFromConfig();
    }

    /**
     * Test SQLite database methods
     * 
     * @requires extension pdo
     */
    public function testMethods()
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

        $table = new Table($db);
        $table->setName('dummy');
        $table->setColumns(array('dummy' => 'integer'));

        $db->AddTable($table);
        $db->getTables();
        $db->getTable('notfound');
        $db->getPDO();
        $entity = new User();
        $db->createTableFromEntity($entity);

        $property = new Property('dummy');

        $expected = 'SELECT * FROM dummy;';
        $result = $db->getSelect('*', (string) $table);
        $this->assertEquals($expected, $result);

        $expected = 'SELECT * FROM dummy WHERE dummy = ? LIMIT 10 OFFSET 0;';
        $result = $db->getSelect('*', (string) $table, 'dummy = ?', '', 10, 0);
        $this->assertEquals($expected, $result);

        $expected = 'CREATE TABLE IF NOT EXISTS dummy (id INTEGER PRIMARY KEY, dummy integer);';
        $result = $db->getCreateTable($table, array('id' => 'auto', 'dummy' => 'integer'));
        $this->assertEquals($expected, $result);

        $expected = 'DROP TABLE IF EXISTS dummy;';
        $result = $db->getDropTable($table);
        $this->assertEquals($expected, $result);

        $expected = 'ALTER TABLE dummy ADD COLUMN dummy integer;';
        $result = $db->getAddColumn($table, $property, 'integer');
        $this->assertEquals($expected, $result);

        // $expected = 'ALTER TABLE dummy MODIFY COLUMN dummy integer;';
        // $result = $db->getModifyColumn($table, $property, 'integer');
        // $this->assertEquals($expected, $result);

        $expected = false;
        $result = $db->getModifyColumn($table, $property, 'integer');
        $this->assertEquals($expected, $result);

        $expected = 'INSERT INTO dummy (dummy) VALUES (?);';
        $result = $db->getInsert($table, array('dummy' => 'value'));
        $this->assertEquals($expected, $result);

        $expected = 'UPDATE dummy SET dummy = ? WHERE ' . $table->getPrimaryKey() . ' = ?;';
        $result = $db->getUpdate($table, array('dummy' => 'value'));
        $this->assertEquals($expected, $result);

        $expected = 'DELETE FROM dummy WHERE dummy = ?;';
        $result = $db->getDelete($table, array('dummy' => 'value'));
        $this->assertEquals($expected, $result);

        // $expected = 'DELETE FROM dummy;';
        // $result = $db->getTruncate($table);
        // $this->assertEquals($expected, $result);

        $expected = 'DELETE FROM dummy';
        $result = $db->getTruncate($table);
        $this->assertEquals($expected, $result);

        $expected = false;
        $result = $db->getDropColumn($table, $property);
        $this->assertEquals($expected, $result);

        $db->terminate();

        // clean up
        unlink(end($parts = explode(':', DB_DSN)));
    }

    /**
     * Test database table
     * 
     * @requires extension pdo
     */
    public function testTable()
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
        $db->setName('duality');

        $table = new Table($db);
        $table->setName('dummy');

        $schema = array(
            'id' => 'auto',
            'email' => 'varchar(80)'
        );
        $sql = $db->getDropTable($table);
        $db->getPDO()->exec($sql);
        $sql = $db->getCreateTable($table, $schema);
        $db->getPDO()->exec($sql);

        $expected = array();
        $table->setColumns($schema);
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $table->setPrimaryKey('id');
        
        $expected = array(1 => array('id' => 1, 'email' => 'dummy1'));
        $table->add(1, array('email' => 'dummy1'));
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = array(
            1 => array('id' => 1, 'email' => 'dummy2')
        );
        $table->set(1, array('id' => 1, 'email' => 'dummy2'));
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = array(
            1 => array('id' => 1, 'email' => 'dummy2')
        );
        $table->find(0, 10, 'id = ?', array(1));
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = array(
            1 => array('id' => 1, 'email' => 'dummy2')
        );
        $filter = new Filter($table);
        $filter->columns('id')
            ->where('id = ?', array(1))
            ->group('id')
            ->limit(0, 10);
        $table->filter($filter);
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = array('id' => 1, 'email' => 'dummy2');
        $result = $table->get(1);
        $this->assertEquals($expected, $result);

        $result = $table->has(1);
        $this->assertTrue($result);

        $expected = array(
            1 => array('id' => 1, 'email' => 'dummy2')
        );
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = array(
            1 => array('id' => 1, 'email' => 'dummy3'),
            2 => array('id' => 2, 'email' => 'dummy4')
        );
        $data = array(
            1 => array('email' => 'dummy3'),
            2 => array('email' => 'dummy4')
        );
        $table->importArray($data);
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = $expected = array(
            2 => array('id' => 2, 'email' => 'dummy4')
        );
        $table->remove(1);
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = array();
        $table->reset();
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $result = $db->getTable('dummy');
        $this->assertInstanceOf('\Duality\Structure\Database\Table', $result);

        $sql = $db->getDropTable($table);
        $db->getPDO()->exec($sql);
        $result = $db->getTable('dummy');
        $this->assertFalse($result);

        // clean up
        unlink(end($parts = explode(':', DB_DSN)));
    }
}