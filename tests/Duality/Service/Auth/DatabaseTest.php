<?php

use Duality\Structure\Database\Table;

class DatabaseTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service invalid config
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testAuthInvalidConfig()
    {
        $config = array(
            'services' => array(
                'auth' => '\Duality\Service\Auth\Database'
            ),
            'db' => array(
                'dsn'   => DB_DSN,
                'user'  => DB_USER,
                'pass'  => DB_PASS
            ),
            'auth' => array(
                'db' => array(
                    'table' => 'dummy',
                    'userfield' => 'email'
                )
            )
        );
        $app = new \Duality\App($config);
        $app->call('auth');
    }

    /**
     * Test auth service
     */
    public function testAuth()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Dummy'
            ),
            'db' => array(
                'dsn'   => DB_DSN,
                'user'  => DB_USER,
                'pass'  => DB_PASS
            ),
            'auth' => array(
                'db' => array(
                    'table' => 'dummy',
                    'userfield' => 'email',
                    'passfield' => 'pass'
                )
            )
        );
        $app = new \Duality\App($config);
        $auth = $app->call('auth');
        $this->assertInstanceOf('\Duality\Service\Auth\Database', $auth);

        $db = $app->call('db');
        $db->setName('duality');

        $table = new Table($db);
        $table->setName('dummy');

        $schema = array(
            'id'    => 'auto',
            'email' => 'varchar(80)',
            'pass'  => 'varchar(80)'
        );
        $db->getPDO()->exec($db->getDropTable($table));
        $db->getPDO()->exec($db->getCreateTable($table, $schema));
        
        $table->setColumns($schema);
        $table->setPrimaryKey('id');        
        $table->add(1, array('email' => 'dummy', 'pass' => 'dummy'));

        $expected = true;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);
        
        $this->assertEquals(true, $auth->isLogged());
        
        $auth->logout();

        $table->remove(1);
        $expected = false;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);
        
        $db->getPDO()->exec($db->getDropTable($table));

        $this->assertEquals(false, $auth->isLogged());

        $this->assertNull($auth->terminate());
    }
}