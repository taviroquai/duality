<?php

class AbstractCacheTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test cache service
     */
    public function testCache()
    {
        $key = 'dummy';
        $value = 'dummy';
        
        $stub = $this->getMockBuilder('\Duality\Core\AbstractCache')
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('put')
             ->will($this->returnValue(NULL));
        $stub->expects($this->any())
             ->method('pull')
             ->will($this->returnValue($value));
        
        $this->assertNull($stub->put($key, $value));
        $this->assertEquals($value, $stub->pull($key));
    }
}