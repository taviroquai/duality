<?php

use Duality\Service\Controller\Base as BaseController;

class BaseTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test controller constuct
     */
    public function testConstruct()
    {
        $expected = '\Duality\Service\Controller\Base';
        $app = $this->getMockBuilder('\Duality\App')
            ->setConstructorArgs(array(dirname(__FILE__), null))
            ->getMock();
        $controller = new BaseController($app);
        $this->assertInstanceOf($expected, $controller);
    }

    /**
     * Test service
     */
    public function testService()
    {
        $app = $this->getMockBuilder('\Duality\App')
            ->setConstructorArgs(array(dirname(__FILE__), null))
            ->getMock();
        $controller = new BaseController($app);

        $controller->init();
        $controller->terminate();
    }

    /**
     * Test action
     */
    public function testAction()
    {
        $app = $this->getMockBuilder('\Duality\App')
            ->setConstructorArgs(array(dirname(__FILE__), null))
            ->getMock();
        $controller = new BaseController($app);

        $url = $this->getMockBuilder('\Duality\Structure\Url')
            ->setConstructorArgs(array('http://google.com/'))
            ->getMock();

        $request = $this->getMockBuilder('\Duality\Structure\Http\Request')
            ->setConstructorArgs(array($url))
            ->getMock();
        $response = new \Duality\Structure\Http\Response;

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
        $controller->doIndex($request, $response);
        $result = $response->getContent();
        $this->assertEquals($expected, $result);
    }

    /**
     * Test to string
     */
    public function testToString()
    {
        $expected = 'Duality\Service\Controller\Base';
        $app = $this->getMockBuilder('\Duality\App')
            ->setConstructorArgs(array(dirname(__FILE__), null))
            ->getMock();
        $controller = new BaseController($app);
        $this->assertEquals($expected, (string) $controller);
    }
}