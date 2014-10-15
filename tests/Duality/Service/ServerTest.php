<?php

class ServerTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test server service
     */
    public function testHome()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');
        $server->getRequestFromGlobals();

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/dummy'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);

        $server->setHome('\Duality\Service\Controller\Base@doIndex');
        $server->listen();

        $server->getResponse();
        $server->setHostname('dummy');
        $server->getHostname();
        $server->createUrl('/dummy');

        $server->terminate();
    }

    /**
     * Test valid request, route and callback
     */
    public function testRoute()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/uri'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);
        $pattern = '/\/uri/';
        $server->addRoute($pattern, '\Duality\Service\Controller\Base@doIndex');

        $server->listen();
    }

    /**
     * Test invalid callback
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidCallback()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/uri'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);
        $pattern = '/\/uri/';
        $server->addRoute($pattern, 'dummy');

        $server->listen();
    }

    /**
     * Test invalid action not found
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidAction()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/uri'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);
        $pattern = '/\/uri/';
        $server->addRoute($pattern, '\Duality\Service\Controller\Base@dummy');

        $server->listen();
    }
}