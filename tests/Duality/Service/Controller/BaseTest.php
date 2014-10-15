<?php

class BaseTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test default controller service
     */
    public function testBase()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $controller = new \Duality\Service\Controller\Base($app);

        $controller->init();
        $controller->doIndex(
            new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://google.com/')),
            new \Duality\Structure\Http\Response
        );
        $classname = (string) $controller;
        $controller->terminate();
    }
}