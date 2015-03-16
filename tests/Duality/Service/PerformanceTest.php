<?php

class PerformanceTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test performance service
     */
    public function testPerformance()
    {
        $app = new \Duality\App();
        $performance = $app->call('performance');

        $string = '';
        for($i = 0; $i < 10; $i++) {
            $string .= $i.' ';
            $performance->checkpoint($i);
        }

        $total = $performance->getCurrentTime();

        $expected = 11;
        $result = count($performance->asArray());
        $this->assertEquals($expected, $result);

        $expected = 0;
        $result = $performance->reset();
        $this->assertEquals($expected, $result);

        $performance->terminate();
    }
}