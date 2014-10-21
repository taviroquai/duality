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

        $url = new \Duality\Structure\Url('http://localhost/dummy');
        $url->setHost('localhost');
        $request = new \Duality\Structure\Http\Request($url);
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
     * Test server global request
     */
    public function testGlobalRequest()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');
        $request = $server->getRequestFromGlobals(array(), array());

        $request = $server->getRequestFromGlobals(array('REQUEST_METHOD' => 'GET'), array());

        $server->getRequestFromGlobals(
            array('REQUEST_METHOD' => 'GET', 'HTTP_X_REQUESTED_WITH' => 'xmlhttprequest'),
            array()
        );
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
     * Test send HTTP headers
     * 
     * @runInSeparateProcess
     */
    public function testHttpHeaders()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');
        $response = $server->getResponse();
        $response->setHeaders(
            array('Content-Type', 'text/html')
        );
        $response->setCookies(array(
            array(
                'name'      => 'duality',
                'value'     => 'dummy',
                'expire'    => time(),
                'path'      => '/',
                'domain'    => 'duality.com',
                'secure'    => true
            )
        ));
        $server->sendHeaders($server->getResponse());
        $server->sendCookies($server->getResponse());
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

    /**
     * Test HTTP
     */
    public function testHTTP()
    {
        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/dummy'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $request->getHeaders();
        $request->getHeaderItem('Content-Type');

        $response = new \Duality\Structure\Http\Response;
        $response->setUrl(new \Duality\Structure\Url('http://localhost/dummy'));
        $response->getUrl();
        $response->setMethod('GET');
        $response->getMethod();
        $response->setStatus(200);
        $response->getStatus();
        $response->setHeaders(array('Content-Type' => 'text/html'));
        $response->getHeaders();
        $response->addHeader('Content-Type', 'text/html');

        $cookie = array();
        $cookie['name'] = 'duality';
        $cookie['value'] = 'dummy';
        $cookie['expire'] = 0;
        $cookie['path'] = '/';
        $cookie['domain'] = 'domain.com';
        $cookie['secure'] = false;
        $response->setCookies(array($cookie));
        $response->getCookies();
        $response->setContent('dummy');
        $response->getContent();
        $response->setTimestamp(time());
        $response->getTimestamp();
        $response->setAjax(true);
        $response->isAjax();
    }

    /**
     * Test invalid HTTP method
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHTTPMethod()
    {
        $response = new \Duality\Structure\Http\Response;
        $response->setMethod('dummy');
    }

    /**
     * Test invalid HTTP headers
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHTTPHeaders()
    {
        $response = new \Duality\Structure\Http\Response;
        $response->setHeaders('dummy');
    }

    /**
     * Test invalid HTTP cookies
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHTTPCookies()
    {
        $response = new \Duality\Structure\Http\Response;
        $response->setCookies('dummy');
    }

    /**
     * Test invalid HTTP cookie item
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHTTPCookieItem()
    {
        $response = new \Duality\Structure\Http\Response;
        $response->setCookies(array('dummy'));
    }

    /**
     * Test invalid HTTP timestamp
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHTTPTimestamp()
    {
        $response = new \Duality\Structure\Http\Response;
        $response->setTimestamp('dummy');
    }
}