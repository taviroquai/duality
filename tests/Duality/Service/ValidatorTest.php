<?php

use Duality\Structure\Http\Request;
use Duality\Structure\Url;
use Duality\Structure\RuleItem;

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
        $app = new \Duality\App($config);
        $server = $app->call('server');

        $request = new Request(new Url('http://localhost/dummy'));
        $request->setParams(array('key' => 'value'));
        $request->setMethod('GET');
        $server->setRequest($request);

        $validator = $app->call('validator');

        $item = new RuleItem(
            'key',
            $request->getParam('key'),
            'required|number|alpha|email|equals|password|length:2:3',
            'The key is valid',
            'The key is invalid'
        );
        $item->setPassMessage('The key is valid');
        $item->setFailMessage('The key is invalid');
        $validator->addRuleItem($item);
        $validator->validate();

        $expected = false;
        $result = $validator->ok();
        $this->assertEquals($expected, $result);

        $expected = array(
            'key' => 'The key is invalid'
        );
        $result = $validator->getMessages();
        $this->assertEquals($expected, $result);
        
        $expected = array(
            'key' => 'The key is invalid'
        );
        $result = $validator->getErrorMessages();
        $this->assertEquals($expected, $result);

        $expected = 'The key is invalid';
        $result = $validator->getMessage('key');
        $this->assertEquals($expected, $result);

        $validator->terminate();
    }

    /**
     * Test invalid rule
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidRule()
    {
        new \Duality\Structure\RuleItem(
            'key',
            'dummy',
            'dummy'
        );
    }
}