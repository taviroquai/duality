<?php

class ImageFileTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test image file
     */
    public function testImageFile()
    {
        $file = new \Duality\Structure\File\ImageFile(DATA_PATH.'/original.jpg');
        $file->saveThumb(DATA_PATH.'/thumb.jpg');
        $file->saveThumb(DATA_PATH.'/thumb.gif');
        $file->saveThumb(DATA_PATH.'/thumb.png');

        $file = new \Duality\Structure\File\ImageFile(DATA_PATH.'/car.jpg');
        $file->saveThumb(DATA_PATH);

        $file = new \Duality\Structure\File\ImageFile(DATA_PATH.'/car.gif');
        $file->saveThumb(DATA_PATH.'/thumb.jpg');
        $file->saveThumb(DATA_PATH.'/thumb.png');

        $file = new \Duality\Structure\File\ImageFile(DATA_PATH.'/car.png');
        $file->saveThumb(DATA_PATH.'/thumb.jpg');
        $file->saveThumb(DATA_PATH.'/thumb.gif');
    }

    /**
     * Test invalid image
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidImageFile()
    {
        $file = new \Duality\Structure\File\ImageFile('dummy');
    }

}