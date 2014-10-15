<?php

class ValidatorTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test validator service
     */
    public function testValidator()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/dummy'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);

        $validator = $app->call('validator');

        $rules = array(
            'key' => array(
                'value' => $request->getParam('key'),
                'rules' => 'required|number|alpha|email|equals|password|length:2:3',
                'fail'  => 'Invalid email address',
                'info'  => 'Email is valid'
            )
        );

        $validator->validateAll($rules);
        $validator->ok();
        $validator->getMessages();
        $validator->getMessage('key');

        $validator->terminate();
    }

    /**
     * Test missing rules
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testMissingRulesParam()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/dummy'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);

        $validator = $app->call('validator');

        $rules = array(
            'key' => array(
                'value' => $request->getParam('key'),
                'fail'  => 'Invalid email address',
                'info'  => 'Email is valid'
            )
        );

        $validator->validateAll($rules);
    }

    /**
     * Test invalid rule
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidRule()
    {
        $config = array(
            'server' => array(
                'url' => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $server = $app->call('server');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/dummy'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);

        $validator = $app->call('validator');

        $rules = array(
            'key' => array(
                'rules' => 'dummy',
                'value' => $request->getParam('key'),
                'fail'  => 'Invalid email address',
                'info'  => 'Email is valid'
            )
        );

        $validator->validateAll($rules);
    }
}