<?php

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

        $client->setUserAgent('dummy');
        $client->getUserAgent();
        $url = new \Duality\Structure\Url('http://google.com/');
        $request = $client->createRequest($url);
        $client->execute($request);
        $client->terminate();
    }
}