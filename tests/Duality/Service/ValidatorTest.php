<?php

class ValidatorTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test validator service
     */
    public function testValidator()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('validator');
    }
}