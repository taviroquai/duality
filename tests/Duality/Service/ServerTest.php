<?php

use Duality\Structure\Url;
use Duality\Structure\Http\Request;

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
        $server->setHostname('localhost');

        $url = new Url('http://localhost/dummy');
        $url->setHost('localhost');
        $request = new Request($url);
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);

        $expected = <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Duality default controller - Replace me!</title>
    </head>
    <body><h1>Duality default controller - Replace me!</h1></body>
</html>
EOF;
        $server->setHome('\Duality\Service\Controller\Base@doIndex');
        ob_start();
        $server->listen();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);

        $result = $server->getResponse();
        $this->assertInstanceOf('\Duality\Structure\Http\Response', $result);

        $expected = 'dummy';
        $server->setHostname($expected);
        $result = $server->getHostname();
        $this->assertEquals($expected, $result);

        $expected = 'http://dummy/dummy';
        $server->setBaseUrl(new Url('http://dummy'));
        $result = $server->createUrl('/dummy');
        $this->assertEquals($expected, (string) $result);

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
        $this->assertEquals(FALSE, $request);

        $request = $server->getRequestFromGlobals(array('REQUEST_METHOD' => 'GET'), array());
        $this->assertInstanceOf('\Duality\Structure\Http\Request', $request);

        $request = $server->getRequestFromGlobals(
            array('REQUEST_METHOD' => 'GET', 'HTTP_X_REQUESTED_WITH' => 'xmlhttprequest'),
            array()
        );
        $this->assertInstanceOf('\Duality\Structure\Http\Request', $request);
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

        $expected = <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Duality default controller - Replace me!</title>
    </head>
    <body><h1>Duality default controller - Replace me!</h1></body>
</html>
EOF;
        ob_start();
        $server->listen();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);
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
}