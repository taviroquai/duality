<?php

class HtmlDocTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test html doc structure
     */
    public function testHtmlDoc()
    {
        $file = new \Duality\Structure\File\TextFile(DATA_PATH.'/doc.html');
        $file->load();

        $doc = new \Duality\Structure\HtmlDoc();
        $doc->loadFile($file);
        $doc->save();
        $doc->setTitle('Duality dummy doc title');
        $doc->appendTo('body', '<p>Dummy content</p>');
        $doc->createFromFilePath(DATA_PATH.'/doc.html');
    }
}