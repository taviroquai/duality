<?php

class StreamFileTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test image file
     */
    public function testStreamFile()
    {
        $file = new \Duality\Structure\File\StreamFile(DATA_PATH.'/log.txt');
        
        $file->load(function($chunck) {});
        $file->write('Dummy stream test');
        $file->save();
        $file->close();

        $file->open();
        $file->load(function($chunck) {});
        $file->write('Dummy stream test');
        $file->save();
        $file->close();
    }

}