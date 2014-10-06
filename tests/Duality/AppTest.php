<?php

class AppTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test invalid application path
     * 
     * @expectedException \Duality\Core\DualityException
     */
	public function testAppInvalidPath()
	{
		$app = new \Duality\App(null, null);
	}

    /**
     * Test valid application path
     */
    public function testAppPath()
    {
        $expected = '\Duality\App';
        $app = new \Duality\App(dirname(__FILE__), null);
        $this->assertInstanceOf($expected, $app);
    }

    /**
     * Test load invalid default service
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testAppInvalidLoadService()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $app->loadService('dummy');
    }

    /**
     * Test load database service without configuration
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testAppLoadDatabaseService()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $app->loadService('db');
    }

    /**
     * Test load logger service without configuration
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testAppLoadLoggerService()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $app->loadService('logger');
    }
}