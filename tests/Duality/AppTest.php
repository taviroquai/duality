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
		new \Duality\App(null, null);
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
                'client'    => 'Duality\Service\Client',
                'performance' => 'Duality\Service\Performance'
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
    public function testAppRegisterService()
    {
        $app = new \Duality\App(dirname(__FILE__), array());
        $app->register('dummy', function() use ($app) {
            $service = new \Duality\Service\Validator($app);
            $service->init();
            return $service;
        });
        $this->assertInstanceOf('\Duality\Service\Validator', $app->call('dummy'));
        $app->__destruct();
        unset($app);
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

    /**
     * Test application service aliases
     */
    public function testAppCallServiceAlias()
    {
        $config = array(
            'db'    => array(
                'dsn' => 'sqlite:' . DATA_PATH . '/db.sqlite'
            ),
            'logger' => array(
                'buffer' => DATA_PATH . '/log.txt'
            ),
            'locale' => array(
                'default'   => 'en_US',
                'dir'       => DATA_PATH . '/lang',
                'timezone'  => 'Europe/Lisbon'
            ),
            'server'        => array(
                'url'       => '/',
                'hostname'  => 'localhost'
            ),
            'security'  => array(
                'salt'      => 'dummy',
                'hash'      => 'sha256'
            ),
            'mailer'    => array(
                'from'  => array('email' => 'no-reply@domain.com', 'name' => 'Duality Mailer'),
                'smtp'  => array(
                    'host' => 'smtp.gmail.com',
                    'user' => 'username',
                    'pass' => 'password',
                    'encr' => 'tls',
                    'port' => 587,
                    'dbgl' => 0
                )
            ),
            'remote'    => array(
                'localhost' => array(
                    'username'  => '',
                    'password'  => ''
                )
            ),
            'auth' => array(
                'table' => 'dummy',
                'user' => 'email',
                'pass' => 'pass'
            )
        );
        $app = new \Duality\App(dirname(__FILE__).'/../..', $config);

        $expected = '\Duality\Service\Database';
        $this->assertInstanceOf($expected, $app->getDb());

        $expected = '\Duality\Service\Logger';
        $this->assertInstanceOf($expected, $app->getLogger());

        $expected = '\Duality\Service\Security';
        $this->assertInstanceOf($expected, $app->getSecurity());

        $expected = '\Duality\Service\Validator';
        $this->assertInstanceOf($expected, $app->getValidator());

        $expected = '\Duality\Service\Session';
        $this->assertInstanceOf($expected, $app->getSession());

        $expected = '\Duality\Service\Logger';
        $this->assertInstanceOf($expected, $app->getLogger());

        $expected = '\Duality\Service\Auth';
        $this->assertInstanceOf($expected, $app->getAuth());

        $expected = '\Duality\Service\Mailer';
        $this->assertInstanceOf($expected, $app->getMailer());

        $expected = '\Duality\Service\Paginator';
        $this->assertInstanceOf($expected, $app->getPaginator());

        $expected = '\Duality\Service\SSH';
        $this->assertInstanceOf($expected, $app->getSSH());

        $expected = '\Duality\Service\Server';
        $this->assertInstanceOf($expected, $app->getServer());

        $expected = '\Duality\Service\Localization';
        $this->assertInstanceOf($expected, $app->getLocale());

        $expected = '\Duality\Service\Commander';
        $this->assertInstanceOf($expected, $app->getCmd());

        $expected = '\Duality\Service\Client';
        $this->assertInstanceOf($expected, $app->getClient());

        $expected = '\Duality\Service\Performance';
        $this->assertInstanceOf($expected, $app->getPerformance());

    }

    /**
     * Test apc cache service alias
     * 
     * @requires extension apc
     */
    public function testAppCallAPCuServiceAlias()
    {
        $config = array();
        $app = new \Duality\App(dirname(__FILE__).'/../..', $config);

        $expected = '\Duality\Service\Cache';
        $this->assertInstanceOf($expected, $app->getCache());

    }

}