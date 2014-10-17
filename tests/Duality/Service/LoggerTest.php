<?php

class LoggerTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test invalid logger configuration
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLoggerWithoutConfig()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $app->call('logger');
    }

    /**
     * Test unreadable buffer
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLoggerUnreadBuffer()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $dummy = fopen(DATA_PATH.'/log.txt', 'w+b');
        $app->call('logger');
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
        $logger = $app->call('logger');

        // Test NOTICE
        $logger->log('dummy');

        // Test E_USER_WARNING
        $logger->log('dummy', E_USER_WARNING);

        // Test E_USER_ERROR
        $logger->log('dummy', E_USER_ERROR);

        // Terminate
        $logger->terminate();
    }
}