<?php

class LDAPTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service invalid config
     * 
     * @requires extension ldap
     * @expectedException \Duality\Core\DualityException
     */
    public function testAuthInvalidConfig()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Dummy',
                'auth'  => '\Duality\Service\Auth\LDAP'
            ),
            'auth' => array(
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('auth');
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
                'session' => '\Duality\Service\Session\Dummy',
                'auth'  => '\Duality\Service\Auth\LDAP'
            ),
            'auth' => array(
                'host' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('auth');
        $this->assertInstanceOf('\Duality\Service\Auth\LDAP', $auth);

        $expected = false;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);

        $this->assertNull($auth->terminate());
    }
}