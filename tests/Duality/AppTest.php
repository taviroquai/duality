<?php

class AppTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test application defaults
     */
    public function testAppDefaults()
    {
        $expected = getcwd();
        $app = new \Duality\App();
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
                'remote'    => 'Duality\Service\SSH',
                'server'    => 'Duality\Service\HTTPServer\Native',
                'idiom'     => 'Duality\Service\Translation',
                'cmd'       => 'Duality\Service\Commander',
                'client'    => 'Duality\Service\Client',
                'performance' => 'Duality\Service\Performance'
            )
        );
        $app = new \Duality\App($expected);
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
        $app = new \Duality\App(array());
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
        $app = new \Duality\App($config);
        $this->assertEquals($expected, $app->cfg($key));
    }
    
    /**
     * Test application register service
     */
    public function testAppRegisterService()
    {
        $app = new \Duality\App(array());
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
        $app = new \Duality\App($config);
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
                'dsn' => DB_DSN
            ),
            'logger' => array(
                'buffer' => DATA_PATH . '/log.txt'
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
                'db' => array(
                    'table' => 'dummy',
                    'userfield' => 'email',
                    'passfield' => 'pass'
                )
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);

        $expected = '\Duality\Core\AbstractDatabase';
        $this->assertInstanceOf($expected, $app->getDb());

        $expected = '\Duality\Service\Logger';
        $this->assertInstanceOf($expected, $app->getLogger());

        $expected = '\Duality\Service\Security';
        $this->assertInstanceOf($expected, $app->getSecurity());

        $expected = '\Duality\Service\Validator';
        $this->assertInstanceOf($expected, $app->getValidator());

        $expected = '\Duality\Core\AbstractSession';
        $this->assertInstanceOf($expected, $app->getSession());

        $expected = '\Duality\Service\Logger';
        $this->assertInstanceOf($expected, $app->getLogger());
        
        $expected = '\Duality\Service\Auth\Database';
        $this->assertInstanceOf($expected, $app->getAuth());

        $expected = '\Duality\Service\Mailer';
        $this->assertInstanceOf($expected, $app->getMailer());

        $expected = '\Duality\Service\Paginator';
        $this->assertInstanceOf($expected, $app->getPaginator());
        
        $expected = '\Duality\Service\HTTPServer';
        $this->assertInstanceOf($expected, $app->getHTTPServer());

        $expected = '\Duality\Service\Commander';
        $this->assertInstanceOf($expected, $app->getCmd());

        $expected = '\Duality\Service\HTTPClient';
        $this->assertInstanceOf($expected, $app->getHTTPClient());

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
        $app = new \Duality\App();

        $expected = '\Duality\Core\AbstractCache';
        $this->assertInstanceOf($expected, $app->getCache());

    }

    /**
     * Test translation service alias
     */
    public function testAppCallTranslationServiceAlias()
    {
        $config = array(
            'idiom' => array(
                'default'   => 'en_US',
                'dir'       => DATA_PATH . '/lang',
                'timezone'  => 'Europe/Lisbon'
            )
        );
        $app = new \Duality\App($config);

        $expected = '\Duality\Service\Translation';
        $this->assertInstanceOf($expected, $app->getIdiom());

    }
    
    public function testAuth()
    {   
        $auth = $this->getMockBuilder('\Duality\Core\AbstractAuth')
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();

        $auth->expects($this->any())
             ->method('login')
             ->will($this->returnValue(true));
        
        $app = new Duality\App();
        $app->register('auth', function () use ($auth) {
            return $auth;
        });
        
        $expected = true;
        $result = $app->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);
        
        $this->assertEquals(true, $app->isLogged());
        
        $this->assertEquals('dummy', $app->whoAmI());
        
        $this->assertNull($app->logout());
        
    }
    
    public function testSSH()
    {
        $config = array(
            'services' => array(
                'auth' => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
                'ssh' => array(
                    'host' => 'localhost'
                )
            )
        );
        $app = new \Duality\App($config);
        
        $expected = '\Duality\Service\SSH';
        $this->assertInstanceOf($expected, $app->getRemote());
    }
}