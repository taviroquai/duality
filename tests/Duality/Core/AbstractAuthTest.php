<?php

class AbstractAuthTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test auth service
     */
    public function testAuth()
    {
        $stub = $this->getMockBuilder('\Duality\Core\AbstractAuth')
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('login')
             ->will($this->returnValue(true));
        $stub->expects($this->any())
             ->method('init')
             ->will($this->returnValue(NULL));
        $stub->expects($this->any())
             ->method('terminate')
             ->will($this->returnValue(NULL));
        $this->assertTrue($stub->login('dummy', 'dummy'));
        $this->assertNull($stub->init());
        $this->assertNull($stub->terminate());
    }
}