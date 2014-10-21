<?php

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
        $controller = new \Duality\Service\Controller\Base($app);
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
        $controller = new \Duality\Service\Controller\Base($app);

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
        $controller = new \Duality\Service\Controller\Base($app);

        $url = $this->getMockBuilder('\Duality\Structure\Url')
            ->setConstructorArgs(array('http://google.com/'))
            ->getMock();

        $request = $this->getMockBuilder('\Duality\Structure\Http\Request')
            ->setConstructorArgs(array($url))
            ->getMock();
        $response = $this->getMock('\Duality\Structure\Http\Response');

        $controller->doIndex($request, $response);
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
        $controller = new \Duality\Service\Controller\Base($app);
        $this->assertEquals($expected, (string) $controller);
    }
}