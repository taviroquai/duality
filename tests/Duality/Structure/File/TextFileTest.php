<?php

class TextFileTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test text file
     */
    public function testTextFile()
    {
        $file = new \Duality\Structure\File\TextFile(DATA_PATH.'/log.txt');

        $file->getPath();
        $file->exists();
        $file->loadAttributes();
        $file->load(function($content) {});
        $file->setContent('Text file dummy content');
        $file->save();
    }

}