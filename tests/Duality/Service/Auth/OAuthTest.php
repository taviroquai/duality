<?php

use Duality\Structure\Url;
use Duality\Structure\Http\Request;

class OAuthTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service invalid config
     * 
     * @requires extension oauth
     * @expectedException \Duality\Core\DualityException
     */
    public function testAuthInvalidConfig()
    {
        $config = array(
            'services' => array(
                'session'   => '\Duality\Service\Session\Dummy',
                'auth'      => '\Duality\Service\Auth\OAuth'
            ),
            'auth' => array(
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $app->call('auth');
    }

    /**
     * Test auth service
     * 
     * @requires extension oauth
     */
    public function testAuth()
    {
        $config = array(
            'services' => array(
                'session'   => '\Duality\Service\Session\Dummy',
                'auth'      => '\Duality\Service\Auth\OAuth'
            ),
            'server' => array(
                'uri'       => '/',
                'hostname'  => 'localhost'
            ),
            'auth' => array(
                'url'       => 'http://localhost/oauth',
                'key'       => 'dummy',
                'secret'    => 'dummy'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);

        $request = new Request(new Url('http://localhost/'));
        $request->setParams(array('key' => 'value'));
        $app->call('server')->setRequest($request);

        $auth = $app->call('auth');
        $this->assertInstanceOf('\Duality\Service\Auth\OAuth', $auth);

        $expected = false;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);

        $app = new \Duality\App(dirname(__FILE__), $config);

        $request = new Request(new Url('http://localhost/'));
        $request->setParams(array('oauth_token' => 'dummy'));
        $app->call('server')->setRequest($request);

        $auth = $app->call('auth');
        $auth->login('dummy', 'dummy');
        try {
            $url = $auth->getAccessUrl();
            // header('Location: $url');
        } catch (\OAuthException $e) {
            echo $e->getMessage();
        }

        $expected = false;
        $result = $auth->login('dummy', 'dummy');
        $this->assertEquals($expected, $result);

        $this->assertNull($auth->terminate());
    }
}