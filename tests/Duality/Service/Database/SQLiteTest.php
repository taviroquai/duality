<?php

class SQLiteTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test SQLite database service with invalid config
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

        $table = new \Duality\Structure\Database\Table($db);
        $table->setName('dummy');
        $table->setColumns(array('dummy' => 'integer'));

        $db->AddTable($table);
        $db->getTables();
        $db->getPDO();
        $entity = new \Duality\Structure\Entity\User();
        $db->createTableFromEntity($entity);

        $property = new \Duality\Structure\Property('dummy');

        $expected = 'SELECT * FROM dummy;';
        $result = $db->getSelect('*', (string) $table);
        $this->assertEquals($expected, $result);

        $expected = 'SELECT * FROM dummy WHERE dummy = ? LIMIT 10 OFFSET 0;';
        $result = $db->getSelect('*', (string) $table, 'dummy = ?', 10, 0);
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

        $expected = 'UPDATE dummy SET dummy = ?;';
        $result = $db->getUpdate($table, array('dummy' => 'value'));
        $this->assertEquals($expected, $result);

        $expected = 'DELETE FROM dummy WHERE dummy = ?;';
        $result = $db->getDelete($table, array('dummy' => 'value'));
        $this->assertEquals($expected, $result);

        // $expected = 'DELETE FROM dummy;';
        // $result = $db->getTruncate($table);
        // $this->assertEquals($expected, $result);

        $expected = false;
        $result = $db->getTruncate($table);
        $this->assertEquals($expected, $result);

        $db->terminate();
    }

    /**
     * Test database table
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

        $table = new \Duality\Structure\Database\Table($db);
        $table->setName('dummy');

        $schema = array(
            'id' => 'auto',
            'email' => 'varchar(80)'
        );
        $sql = $db->getDropTable($table);
        $db->getPDO()->exec($sql);
        $sql = $db->getCreateTable($table, $schema);
        $db->getPDO()->exec($sql);

        $table->setColumns($schema);
        $table->setPrimaryKey('id');
        
        $table->add(1, array('id' => 1, 'email' => 'dummy1'));
        $table->set(2, array('id' => 1, 'email' => 'dummy2'));
        $table->find(0, 10, 'id = ?', array(1));
        $table->get(1);
        $table->has(1);
        $table->asArray();
        $data = array(
            1 => array('email' => 'dummy3'),
            2 => array('email' => 'dummy4'),
            3 => array('email' => 'dummy5')
        );
        $table->importArray($data);
        $table->remove(1);
        $table->reset();

        $db->getTable('dummy');

        $sql = $db->getDropTable($table);
        $db->getPDO()->exec($sql);
    }

    
}