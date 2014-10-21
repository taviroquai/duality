<?php

class MySqlTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test MySql database service with invalid config
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testMySqlInvalidConfig()
    {
        $config = array(
            'db' => array()
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $db = $app->call('db');
    }

    /**
     * Test MySql database service
     */
    public function testMySql()
    {
        $config = array(
            'db' => array(
                'dsn'   => DB_DSN,
                'user'  => DB_USER,
                'pass'  => DB_PASS
            )
        );
        $app = $this->getMockBuilder('\Duality\App')
            ->setConstructorArgs(array(dirname(__FILE__), $config))
            ->getMock();
        $db = new \Duality\Service\Database\MySql($app);
    }

    /**
     * Test MySql database methods
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
        $app = $this->getMockBuilder('\Duality\App')
            ->setConstructorArgs(array(dirname(__FILE__), $config))
            ->getMock();
        $db = new \Duality\Service\Database\MySql($app);

        $table = new \Duality\Structure\Database\Table($db);
        $table->setName('dummy');
        $table->setColumns(array('dummy' => 'integer'));

        $property = new \Duality\Structure\Property('dummy');

        $expected = 'SELECT * FROM dummy;';
        $result = $db->getSelect('*', (string) $table);
        $this->assertEquals($expected, $result);

        $expected = 'SELECT * FROM dummy WHERE dummy = ? LIMIT 10 OFFSET 0;';
        $result = $db->getSelect('*', (string) $table, 'dummy = ?', 10, 0);
        $this->assertEquals($expected, $result);

        $expected = 'CREATE TABLE IF NOT EXISTS dummy (id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, dummy integer);';
        $result = $db->getCreateTable($table, array('id' => 'auto', 'dummy' => 'integer'));
        $this->assertEquals($expected, $result);

        $expected = 'DROP TABLE IF EXISTS dummy;';
        $result = $db->getDropTable($table);
        $this->assertEquals($expected, $result);

        $expected = 'ALTER TABLE dummy ADD COLUMN dummy integer;';
        $result = $db->getAddColumn($table, $property, 'integer');
        $this->assertEquals($expected, $result);

        $expected = 'ALTER TABLE dummy DROP COLUMN dummy;';
        $result = $db->getDropColumn($table, $property);
        $this->assertEquals($expected, $result);

        $expected = 'ALTER TABLE dummy MODIFY COLUMN dummy integer;';
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

        $expected = "SELECT COLUMN_NAME, ";
        $expected .= "COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS ";
        $expected .= "WHERE TABLE_SCHEMA = '' ";
        $expected .= "AND TABLE_NAME = 'dummy';";
        $result = $db->getColumns($table, $property);
        $this->assertEquals($expected, $result);

        $expected = 'TRUNCATE dummy;';
        $result = $db->getTruncate($table);
        $this->assertEquals($expected, $result);

        $db->terminate();
    }
}