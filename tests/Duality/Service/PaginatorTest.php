<?php

class PaginatorTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test paginator service
     */
    public function testPaginator()
    {
        $config = array(
            'server' => array(
                'url'      => '/',
                'hostname' => 'localhost'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
        $paginator = $app->call('paginator');

        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/items'));
        $request->setParams(array('page' => 2));
        $app->call('server')->setRequest($request);
        $paginator->config('http://localhost/items?', 20, 3);
        $paginator->getFirstPageUrl();
        $paginator->getLastPageUrl();
        $paginator->getNextPageUrl();
        $paginator->getPreviousPageUrl();
        $paginator->getPageUrl(-1);
        $paginator->getCurrentOffset();

        // Ask out of range page
        $request = new \Duality\Structure\Http\Request(new \Duality\Structure\Url('http://localhost/items'));
        $request->setParams(array('page' => 21));
        $app->call('server')->setRequest($request);
        $paginator->config('http://localhost/items?', 20, 3);

        // Terminate
        $paginator->terminate();
    }
}