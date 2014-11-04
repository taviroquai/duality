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
        $result = $client->execute($request);
        $this->assertInstanceOf('\Duality\Structure\Http\Response', $result);

        $client->terminate();
    }
}