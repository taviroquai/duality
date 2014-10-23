<?php

class PerformanceTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test performance service
     */
    public function testPerformance()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $performance = $app->call('performance');

        $string = '';
        for($i = 0; $i < 10; $i++) {
            $string .= $i.' ';
            $performance->checkpoint($i);
        }

        $total = $performance->getCurrentTime();
        $performance->asArray();
        $performance->reset();
        $performance->terminate();
    }
}