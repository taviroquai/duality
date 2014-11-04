<?php

/**
 * Abstract file structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\DualityException;
use Duality\Core\Structure;

/**
 * File structure class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class File 
extends Structure
{
    /**
     * Holds the file system path
     * 
     * @var string Holds the file path
     */
    protected $path;

    /**
     * Holds the file meta attributes
     * 
     * @var array Holds the file meta attributes
     */
    protected $attributes;

    /**
     * Holds the file content
     * 
     * @var string Holds the file contents
     */
    protected $content;

    /**
     * Creates a new image
     * 
     * @param string $path The image file path
     */
    public function __construct($path)
    {
        if (!is_string($path) || empty($path)) {
            throw new DualityException('Duality Error: invalid file');
        }
        $this->setPath($path);
    }

    /**
     * Sets the file path
     * 
     * @param string $path Give the file path
     * 
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Gets the file path
     * 
     * @return string Returns the file path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Checks whether file exists or not
     * 
     * @return boolean Check result
     */
    public function exists()
    {
        return file_exists($this->path);
    }

    /**
     * Loads file meta attributes
     * 
     * @return void
     */
    public function loadAttributes()
    {
        $this->attributes = file($this->path);
    }

    /**
     * Loads file contents
     * 
     * @param \Closure $callback Give the after load callback
     * 
     * @return string Returns the file contents
     */
    public function load(\Closure $callback = null)
    {
        if (is_null($this->content)) {
            $this->content = (string) file_get_contents($this->path);
            if (!is_null($callback)) {
                $callback($this->content);
            }
        }
        return $this->content;
    }

    /**
     * Gets the file contents
     * 
     * @return string The file contents
     */
    public function getContent()
    {
        return (string) $this->content;
    }

    /**
     * Sets the file contents
     * 
     * @param string $content Give the file contents
     * 
     * @return void
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
    }

    /**
     * Saves the file to media
     * 
     * @return int|false The number of bytes saved of false
     */
    public function save()
    {
        return @file_put_contents($this->getPath(), $this->content);    
    }
}