<?php

class SSHAuthTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service invalid config
     * 
     * @requires extension ssh2
     * @expectedException \Duality\Core\DualityException
     */
    public function testAuthInvalidConfig()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Dummy',
                'auth'  => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
            )
        );
        $app = new \Duality\App($config);
        $app->call('auth');
    }
    
    /**
     * Test auth service invalid config
     * 
     * @requires extension ssh2
     * @expectedException \Duality\Core\DualityException
     */
    public function testAuthFailed()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Dummy',
                'auth'  => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
                'ssh' => array('host' => 'localhos')
            )
        );
        $app = new \Duality\App($config);
        $app->call('auth');
    }
    
    /**
     * Test auth service
     * 
     * @requires extension ssh2
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidFingerPrint()
    {
        $config = array(
            'services' => array(
                'auth'  => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
                'ssh' => array(
                    'host' => 'localhost',
                    'fingerprint' => '9b:ca:52:db:c7:de:d4:61:b0:0a:5a:96:27:41:11:a2',
                    'paraphrase' => ''
                )
            )
        );
        $app = new \Duality\App($config);
        $auth = $app->call('auth');
        $this->assertInstanceOf('\Duality\Service\Auth\SSH', $auth);

        $expected = false;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test auth service
     * 
     * @requires extension ldap
     */
    public function testAuth()
    {
        $config = array(
            'services' => array(
                'auth'  => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
                'ssh' => array(
                    'host' => 'localhost'
                )
            )
        );
        $app = new \Duality\App($config);
        $auth = $app->call('auth');
        $this->assertInstanceOf('\Duality\Service\Auth\SSH', $auth);

        $expected = false;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);
        
        $result = $auth->getConnection();
        $this->assertEquals(is_resource($result), true);

        $this->assertNull($auth->terminate());
        
        $auth->__destruct();
    }
}