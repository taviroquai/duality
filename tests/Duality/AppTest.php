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
    public function testAppValidPath()
    {
        $expected = '\Duality\App';
        $app = new \Duality\App(dirname(__FILE__), null);
        $this->assertInstanceOf($expected, $app);
    }

    /**
     * Test application defaults
     */
    public function testAppDefaults()
    {
        $expected = dirname(__FILE__);
        $app = new \Duality\App($expected, null);
        $this->assertEquals($expected, $app->getPath());

        $expected = array(
            'services' => array(
                'db'        => 'Duality\Service\Database\Sqlite',
                'logger'    => 'Duality\Service\Logger',
                'security'  => 'Duality\Service\Security',
                'validator' => 'Duality\Service\Validator',
                'session'   => 'Duality\Service\Session\Dummy',
                'auth'      => 'Duality\Service\Auth',
                'cache'     => 'Duality\Service\Cache',
                'mailer'    => 'Duality\Service\Mailer',
                'paginator' => 'Duality\Service\Paginator',
                'ssh'       => 'Duality\Service\SSH',
                'server'    => 'Duality\Service\Server',
                'locale'    => 'Duality\Service\Localization',
                'cmd'       => 'Duality\Service\Commander',
                'client'    => 'Duality\Service\Client'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $expected);
        $this->assertEquals($expected, $app->getConfig());

        $expected = '\Duality\Structure\File\StreamFile';
        $this->assertInstanceOf($expected, $app->getBuffer());
    }

    /**
     * Test application get empty config item
     */
    public function testAppEmptyConfigItem()
    {
        $expected = null;
        $app = new \Duality\App(dirname(__FILE__), array());
        $this->assertEquals($expected, $app->getConfigItem('dummy'));
    }

    /**
     * Test application get config item
     */
    public function testAppConfigItem()
    {
        $key = 'item';
        $config = array($key => 'dummy');
        $expected = $config[$key];
        $app = new \Duality\App(dirname(__FILE__), $config);
        $this->assertEquals($expected, $app->getConfigItem($key));
    }

    /**
     * Test application register service
     */
    public function testAppRegisterInvalidService()
    {
        $app = new \Duality\App(dirname(__FILE__), array());
        $app->register('dummy', function() {});
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
     * Test load invalid service
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testAppLoadInvalidService()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $app->register('dummy', function () {});
        $app->loadService('service');
    }

    /**
     * Test load database service with invalid configuration
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testAppLoadServiceInvalidConfig()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $app->loadService('logger');
    }

    /**
     * Test load database service with valid configuration
     */
    public function testAppLoadAndTerminateService()
    {
        $config = array(
            'logger' => array(
                'buffer' => DATA_PATH.'/log.txt'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $app->loadService('logger');
    }

    /**
     * Test application call service
     */
    public function testAppCallService()
    {
        $config = array();
        $app = new \Duality\App(dirname(__FILE__), $config);
        $expected = '\Duality\Service\Validator';
        $this->assertInstanceOf($expected, $app->call('validator', array(), false));
        $this->assertInstanceOf($expected, $app->call('validator'));
    }

}