<?php

class LoggerTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test logger service
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLoggerWithoutConfig()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('logger');
    }

    /**
     * Test logger service
     */
    public function testLocalization()
    {
        $config = array(
            'logger' => array(
                'buffer'   => './tests/data/log.txt'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('logger');
    }
}