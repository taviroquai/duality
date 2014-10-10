<?php

class SQLiteTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test SQLite database service
     */
    public function testSQLite()
    {
        $config = array(
            'db' => array(
                'dsn'   => 'sqlite:tests/data/test.sqlite',
                'user'  => 'root',
                'pass'  => 'pass'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('db');
    }
}