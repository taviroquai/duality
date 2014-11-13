<?php

/**
 * Image file structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\File;

use Duality\Core\DualityException;
use Duality\Structure\File;

/**
 * Image class
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class ImageFile
extends File
{
    /**
     * Creates a new image
     * 
     * @param string $path The image file path
     */
    public function __construct($path)
    {
        parent::__construct($path);
        if (!is_array(@getimagesize($this->path))) {
            throw new DualityException(
                'Duality Error: invalid image',
                DualityException::E_FILE_INVALIDTYPE
            );
        }
    }
    
    /**
     * Creates and saves a thumb image
     * 
     * @param string $target    The target image file path or directory
     * @param int    $thumbSize The desired proportional size in px
     * 
     * @return boolean Returns the save result
     */
    public function saveThumb($target, $thumbSize = 60)
    {
        // get thumb
        $thumb = $this->createThumb($thumbSize);

        // generate thumb name and save image
        $target = is_dir($target) ? 
            rtrim($target, '/').'/'.basename($this->path) : $target;
        $parts = explode('.', $target);
        switch (end($parts)) {
        case 'gif':
            $result = imagegif($thumb, $target);
            break;
        case 'png':
            $result = imagepng($thumb, $target, 9);
            break;
        case 'jpeg':
        case 'jpg':
        default:
            $result = imagejpeg($thumb, $target, 90);
        }
        return $result;
    }
    
    /**
     * Creates an image resource
     * 
     * @param int $size Give the thumb size
     * 
     * @return resource The image resource
     */
    public function createThumb($size = 60)
    {   
        // Get image information
        list($width, $height, $type) = getimagesize($this->path);
        
        // Choose image type
        switch ($type) {
        case 1: $imgcreatefrom = "ImageCreateFromGIF";
            break;
        case 3: $imgcreatefrom = "ImageCreateFromPNG";
            break;
        default: $imgcreatefrom = "ImageCreateFromJPEG";
        }

        // Load image
        $original = $imgcreatefrom($this->path); 
        
        // Find purpotion
        $biggestSide = $height;
        $cropPercent = $height > 560 ? 0.5 : $width / $height;
        if ($width > $height) {
            $biggestSide = $width;
            $cropPercent = $width > 560 ? 0.5 : $height / $width;
        }
        $cropW = $cropH = $biggestSide*$cropPercent;

        // Getting the top left coordinate
        $x = ($width-$cropW)/2;
        $y = ($height-$cropH)/2;
        
        // Create new image
        $thumb = imagecreatetruecolor($size, $size);
        
        // replace alpha with color
        $white = imagecolorallocate($thumb,  255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, $size, $size, $white);

        // Copy into new image
        imagecopyresampled(
            $thumb, $original, 0, 0, $x, $y, $size, $size, $cropW, $cropH
        );
        
        return $thumb;
    }
}