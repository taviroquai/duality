<?php

class TextFileTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test text file
     */
    public function testTextFile()
    {
        $path = DATA_PATH.'/log.txt';
        $file = new \Duality\Structure\File\TextFile($path);

        $result = $file->getPath();
        $this->assertEquals($path, $result);

        $result = $file->exists();
        $this->assertEquals(file_exists($path), $result);

        $file->loadAttributes();
        $file->load(function($content) {});
        $expected = 'Text file dummy content';
        $file->setContent($expected);
        $this->assertEquals($expected, $file->getContent());
        
        $expected = strlen($file->getContent());
        $result = $file->save();
        $this->assertEquals($expected, $result);
    }

    /**
     * Test invalid file path
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidPath()
    {
        new \Duality\Structure\File\TextFile(null);
    }
}