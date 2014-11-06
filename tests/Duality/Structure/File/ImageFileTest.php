<?php

use Duality\Structure\File\ImageFile;

class ImageFileTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test image file
     */
    public function testImageFile()
    {
        $file = new ImageFile(DATA_PATH.'/original.jpg');
        $result = $file->saveThumb(DATA_PATH.'/thumb.jpg');
        $this->assertEquals(TRUE, $result);

        $result = $file->saveThumb(DATA_PATH.'/thumb.gif');
        $this->assertEquals(TRUE, $result);

        $result = $file->saveThumb(DATA_PATH.'/thumb.png');
        $this->assertEquals(TRUE, $result);

        $file = new ImageFile(DATA_PATH.'/car.gif');
        $result = $file->saveThumb(DATA_PATH.'/thumb.jpg');
        $this->assertEquals(TRUE, $result);

        $result = $file->saveThumb(DATA_PATH.'/thumb.png');
        $this->assertEquals(TRUE, $result);

        $file = new ImageFile(DATA_PATH.'/car.png');
        $result = $file->saveThumb(DATA_PATH.'/thumb.jpg');
        $this->assertEquals(TRUE, $result);

        $result = $file->saveThumb(DATA_PATH.'/thumb.gif');
        $this->assertEquals(TRUE, $result);
    }

    /**
     * Test invalid image
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidImageFile()
    {
        new ImageFile('dummy');
    }

}