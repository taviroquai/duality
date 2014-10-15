<?php

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

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/items'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('POST');
        $app->call('server')->setRequest($request);
        $security = $app->call('security');
        $result = $security->encrypt('dummy');
        $security->decrypt($result);
        $security->terminate();
    }
}