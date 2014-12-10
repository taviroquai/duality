<?php

use Duality\Structure\File\TextFile;
use Duality\Structure\HtmlDoc;

class HtmlDocTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test html doc structure
     */
    public function testHtmlDoc()
    {
        $filename = DATA_PATH.'/doc.html';
        $file = new TextFile($filename);
        $file->load();

        $doc = new HtmlDoc();
        $doc->loadFile($file);
        $doc2 = HtmlDoc::createFromFilePath($filename);
        $this->assertInstanceOf('\Duality\Structure\HtmlDoc', $doc2);

        $doc->setTitle('Duality dummy doc title');
        $result = (string) $doc;
        $expected = "<!DOCTYPE html>\n<html><head><title>Duality dummy doc title</title></head><body></body></html>\n";
        $this->assertEquals($expected, $result);
        
        $doc = HtmlDoc::createFromFilePath($filename);
        $doc->setAttribute('body', 'id', 'dummy');
        $result = (string) $doc;
        $expected = "<!DOCTYPE html>\n<html><head><title></title></head><body id=\"dummy\"></body></html>\n";
        $this->assertEquals($expected, $result);

        $doc = HtmlDoc::createFromFilePath($filename);
        $expected = "<!DOCTYPE html>\n<html><head><title></title></head><body><p>Dummy content</p></body></html>\n";
        $doc->appendTo('body', '<p>Dummy content</p>');
        $result = (string) $doc;
        $this->assertEquals($expected, $result);
    }
}