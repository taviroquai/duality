<?php

use Duality\Structure\Http\Request;
use Duality\Structure\Url;

class SecurityTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test security service
     */
    public function testSecurity()
    {
        $config = array(
            'server' => array(
                'url'      => '/',
                'hostname' => 'localhost'
            ),
            'security' => array(
                'algo' => 'sha1',
                'salt' => 'dummy'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);

        $request = new Request(new Url('http://localhost/items'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('POST');
        $app->call('server')->setRequest($request);
        $security = $app->call('security');

        $expected = 'f64133af6818761d95c8230953e5c9ddee1d0cf3';
        $result = $security->encrypt('dummy');
        $this->assertEquals($expected, $result);

        $expected = 'f64133af6818761d95c8230953e5c9ddee1d0cf3';
        $result = $security->decrypt($result);
        $this->assertEquals($expected, $result);

        $security->terminate();
    }
}