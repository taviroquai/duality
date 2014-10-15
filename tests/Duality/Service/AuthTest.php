<?php

class AuthTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service
     */
    public function testAuth()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Structure\Session\Dummy'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $auth = $app->call('auth');
        $this->assertInstanceOf('\Duality\Service\Auth', $auth);

        $expected = false;
        $result = $auth->login('dummy', 'dummy', function($user, $pass) {
            $storage = array();
            return $storage;
        });
        $this->assertEquals($expected, $result);

        $expected = true;
        $result = $auth->login('dummy', 'dummy', function($user, $pass) {
            $storage = array(
                array('dummy', 'dummy')
            );
            return $storage;
        });
        $this->assertEquals($expected, $result);

        $this->assertEquals(true, $auth->isLogged());

        $this->assertEquals('dummy', $auth->whoAmI());

        $auth->logout();

        $this->assertEquals(false, $auth->isLogged());

        $this->assertEquals(null, $auth->whoAmI());

        $this->assertNull($auth->terminate());
    }
}