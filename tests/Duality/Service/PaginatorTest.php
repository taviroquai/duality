<?php

use Duality\Structure\Http\Request;
use Duality\Structure\Url;

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
        $app = new \Duality\App($config);
        $paginator = $app->call('paginator');

        $request = new Request(new Url('http://localhost/items'));
        $request->setParams(array('page' => 2));
        $app->call('server')->setRequest($request);
        $paginator->config('http://localhost/items', 20, 3);

        $expected = 'http://localhost/items?page=1';
        $result = $paginator->getFirstPageUrl();
        $this->assertEquals($expected, $result);

        $expected = 'http://localhost/items?page=7';
        $result = $paginator->getLastPageUrl();
        $this->assertEquals($expected, $result);

        $expected = 'http://localhost/items?page=3';
        $result = $paginator->getNextPageUrl();
        $this->assertEquals($expected, $result);

        $expected = 'http://localhost/items?page=1';
        $result = $paginator->getPreviousPageUrl();
        $this->assertEquals($expected, $result);

        $expected = '';
        $result = $paginator->getPageUrl(-1);
        $this->assertEquals($expected, $result);

        $expected = 3;
        $result = $paginator->getCurrentOffset();
        $this->assertEquals($expected, $result);

        // Ask out of range page
        $request = new Request(new Url('http://localhost/items'));
        $request->setParams(array('page' => 21));
        $app->call('server')->setRequest($request);
        $paginator->config('http://localhost/items?', 20, 3);

        $expected = 0;
        $result = $paginator->getCurrentOffset();
        $this->assertEquals($expected, $result);

        // Terminate
        $paginator->terminate();
    }
}