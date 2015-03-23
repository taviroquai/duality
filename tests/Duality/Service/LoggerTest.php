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

        // Test error handler
        $result = $logger->error_handler(E_USER_ERROR, 'test error', 'me.php', 1);
        $this->assertTrue($result);
        $result = $logger->error_handler(E_USER_WARNING, 'test error', 'me.php', 1);
        $this->assertTrue($result);
        $result = $logger->error_handler(E_USER_NOTICE, 'test error', 'me.php', 1);
        $this->assertTrue($result);
        
        // Terminate
        $expected = 'Ops! Something went wrong...';
        ob_start();
        $logger->terminate();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);
    }
}