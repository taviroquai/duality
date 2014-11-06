<?php

use Duality\Structure\Url;

class ClientTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test HTTP client service
     */
    public function testClient()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $client = $app->call('client');

        $expected = 'dummy';
        $client->setUserAgent($expected);
        $result = $client->getUserAgent();
        $this->assertEquals($expected, $result);

        $url = new Url('http://google.pt/');
        $request = $client->createRequest($url);
        $response = $client->execute($request);
        $this->assertInstanceOf('\Duality\Structure\Http\Response', $response);

        $expected = 'Moved Permanently';
        $result = $response->getCodeString();
        $this->assertEquals($expected, $result);

        $url = new Url('http://google.pt/');
        $request = $client->createRequest($url);
        $request->addHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $response = $client->execute($request);
        $client->getCurlHandler();

        $client->terminate();
    }
}