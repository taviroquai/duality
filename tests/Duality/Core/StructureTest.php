<?php

class StructureTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test structure
     */
    public function testStructure()
    {
        $expected = 123;
        $stub = $this->getMockForAbstractClass('\Duality\Core\Structure');

        $stub->expects($this->any())
             ->method('setName')
             ->will($this->returnValue(NULL));
        $this->assertNull($stub->setName($expected));

        $stub->method('getName')
             ->will($this->returnValue($expected));
        $this->assertEquals($expected, $stub->getName());

        $this->assertEquals((string) $expected, (string) $stub->getName());        
    }
}