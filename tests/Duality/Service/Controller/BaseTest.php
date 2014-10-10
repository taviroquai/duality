<?php

class BaseTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test default controller service
     */
    public function testBaseController()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $controller = new \Duality\Service\Controller\Base($app);
    }
}