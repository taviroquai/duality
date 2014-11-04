<?php

use Duality\Structure\File\StreamFile;

class StreamFileTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test stream file
     */
    public function testStreamFile()
    {
        $file = new StreamFile(DATA_PATH.'/log.txt');
        
        $data = 'Dummy stream test';
        $file->load(function($chunck) {});
        $result = $file->write($data);
        $this->assertEquals(FALSE, $result);

        $result = $file->save();
        $this->assertEquals(FALSE, $result);

        $result = $file->close();
        $this->assertEquals(FALSE, $result);


        $file->open();
        $file->load(function($chunck) {});
        $result = $file->write($data);
        $this->assertEquals(TRUE, $result);

        $expected = strlen($file->getContent());
        $result = $file->save();
        $this->assertEquals($expected, $result);
        
        $result = $file->close();
        $this->assertEquals(TRUE, $result);

        $file = new StreamFile(DATA_PATH.'/original.jpg');
        $file->open('r');
        $file->load(function($chunck) {});
        $result = $file->close();
        $this->assertEquals(TRUE, $result);
    }

    /**
     * Test forbidden file
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testForbiddenFile()
    {
        $file = new StreamFile(DATA_PATH.'/forbidden');
        $file->open('w');
    }

}