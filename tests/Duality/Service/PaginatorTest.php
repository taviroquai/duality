<?php

class PaginatorTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test paginator service
     */
    public function testPaginator()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $auth = $app->call('paginator');
    }
}