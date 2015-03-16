<?php

class LoggerTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test invalid logger configuration
     * 
     * @runInSeparateProcess
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLoggerWithoutConfig()
    {
        //$this->markTestSkipped('Do not use Duality error_handler.');
        $app = new \Duality\App();
        $app->call('logger');
    }

    /**
     * Test unreadable buffer
     * 
     * @runInSeparateProcess
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testLoggerUnreadBuffer()
    {
        //$this->markTestSkipped('Do not use Duality error_handler.');
        $app = new \Duality\App();
        $dummy = fopen(DATA_PATH.'/log.txt', 'w+b');
        $app->call('logger');
    }

    /**
     * Test invalid log file
     * 
     * @runInSeparateProcess
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidFile()
    {
        //$this->markTestSkipped('Do not use Duality error_handler.');
        $config = array(
            'logger' => array(
                'buffer'   => 'dummy'
            )
        );
        $app = new \Duality\App($config);
        $logger = $app->call('logger');
    }

    /**
     * Test logger service
     * 
     * @runInSeparateProcess
     */
    public function testLogger()
    {
        //$this->markTestSkipped('Do not use Duality error_handler.');
        $config = array(
            'logger' => array(
                'buffer'   => 'tests/data/log.txt'
            )
        );
        $app = new \Duality\App($config);
        $logger = $app->call('logger');

        // Test emergency
        $logger->emergency('emergency');

        // Test alert
        $logger->alert('alert');

        // Test critical
        $logger->critical('critical');
        
        // Test error
        $logger->error('error');
        
        // Test warning
        $logger->warning('warning');
        
        // Test notice
        $logger->notice('notice');
        
        // Test info
        $logger->info('info');
        
        // Test debug
        $logger->debug('debug');

        // Terminate
        $expected = 'Ops! Something went wrong...';
        ob_start();
        $logger->terminate();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);
    }
}