<?php

use Duality\Structure\Url;
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;
use Duality\Structure\Http\Json;

class TestRequest
extends Request
{
    public function __construct(\Duality\Core\InterfaceUrl $url = null) {
        parent::__construct($url);
        
        $this->nativeServer = array('REQUEST_METHOD' => 'GET');
        $this->nativeRequest = array('key' => 'value');
        $this->routeParams->set('name', 'value');
    }

    public function isAuthorized(Response &$res) {
        $res = new Response();
        $res->setStatus(403);
    }
}

class HTTPServerTest 
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
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);
        $server = $app->call('server');
        $server->setHostname('localhost');

        $url = new Url('http://localhost/');
        $url->setHost('localhost');
        $request = new TestRequest($url);
        $server->setRequest($request);

        $expected = 'Welcome to Duality';
        $server->setHome(
            '\Duality\Structure\Http\Response',
            '\Duality\Structure\Http\Request'
        );
        
        ob_start();
        $server->execute();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);
        
        $result = $server->getResponse();
        $this->assertInstanceOf('\Duality\Structure\Http\Response', $result);

        $expected = 'dummy';
        $server->setHostname($expected);
        $result = $server->getHostname();
        $this->assertEquals($expected, $result);

        $expected = 'http://dummy/';
        $server->setBaseUrl(new Url('http://dummy'));
        $result = $server->getBaseUrl();
        $this->assertEquals($expected, (string) $result);
        
        $expected = 'http://dummy/dummy';
        $server->setBaseUrl(new Url('http://dummy'));
        $result = $server->createUrl('/dummy');
        $this->assertEquals($expected, (string) $result);
        
        $result = $server->createRedirect();
        $this->assertInstanceOf('\Duality\Structure\Http\Response', $result);

        $server->terminate();
    }
    
    /**
     * Test server service
     */
    public function testAuthorization()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);
        $server = $app->call('server');
        $server->setHostname('localhost');
        
        $url = new Url('http://localhost/');
        $url->setHost('localhost');
        $request = new TestRequest($url);
        $server->setRequest($request);
    
        $server->setHome(
            '\Duality\Structure\Http\Response',
            '\TestRequest'
        );
        $server->execute();
        $this->assertEquals(403, $server->getResponse()->getStatus());
    }

    /**
     * Test server default request
     */
    public function testDefaultRequest()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);
        $server = $app->call('server');
        $request = $server->getRequest();
        $this->assertInstanceOf('\Duality\Structure\HTTP\Request', $request);
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
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);
        $server = $app->call('server');

        $request = new TestRequest(new Url('http://localhost/uri'));
        $server->setRequest($request);
        $pattern = '/\/uri/';
        $server->addRoute($pattern, '\Duality\Structure\Http\Response');

        $expected = 'Welcome to Duality';
        ob_start();
        $server->execute();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test not found route
     */
    public function testNotFoundRoute()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);
        $server = $app->call('server');

        $request = new TestRequest(new Url('http://localhost/uri'));
        $server->setRequest($request);
        $server->execute();
    }

    /**
     * Test invalid callback
     * 
     * @expectedException \Exception
     */
    public function testInvalidCallback()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            ),
            'services' => array(
                'server' => '\Duality\Service\HTTPServer\NoHeaders'
            )
        );
        $app = new \Duality\App($config);
        $server = $app->call('server');

        $request = new TestRequest(new Url('http://localhost/uri'));
        $server->setRequest($request);
        $pattern = '/\/uri/';
        $server->addRoute($pattern, 'dummy');

        $server->execute();
    }

    /**
     * Test HTTP
     */
    public function testHTTP()
    {
        $url = new \Duality\Structure\Url('http://localhost/dummy');
        $request = new TestRequest($url);
        $request->key = 'value';
        $value = $request->key;
        $request->dummy = 'dummy';
        isset($request->key);
        unset($request->dummy);
        
        $request->setMethod('GET');
        $request->getHeaders();
        $request->getHeader('Content-Type');
        $request->getRouteParams();
        $request->getRouteParam('name');
        $request->importFromGlobals();
        $request->getBaseUrlFromGlobals();
        $request->validateHTTP();

        $response = new Response;
        $response->setUrl(new \Duality\Structure\Url('http://localhost/dummy'));
        $response->getUrl();
        $response->setMethod('GET');
        $response->getMethod();
        $response->setStatus(200);
        $response->getStatus();
        $response->setHeaders(array('Content-Type' => 'text/html'));
        $response->getHeaders();
        $response->addHeader('Content-Type', 'text/html');

        $response->setCookie('duality', 'dummy');
        $response->getCookies();
        $response->setContent('dummy');
        $response->getContent();
        $response->setTimestamp(time());
        $response->getTimestamp();
        
        $request->setAjax(true);
        $request->isAjax();
        $response->onRequest($request);
        
        $response = new Json();
        $response->onRequest($request);
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
     * Test HTTP response headers
     * 
     * @runInSeparateProcess
     */
    public function testResponseHeaders()
    {
        $response = new Response;
        $response->setUrl(new \Duality\Structure\Url('http://localhost/dummy'));
        $response->setMethod('GET');
        $response->setStatus(200);
        $response->setHeaders(array('Content-Type' => 'text/html'));
        $response->addHeader('Content-Type', 'text/html');
        $response->setCookie('duality', 'dummy');
        $response->setContent('dummy');
        $response->setTimestamp(time());
        
        $app = new Duality\App();
        $server = $app->getHTTPServer();
        $server->setResponse($response);
        $server->sendHeaders($response);
        $server->sendCookies($response);
    }
}