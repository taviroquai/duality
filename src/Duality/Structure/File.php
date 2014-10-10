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
     * @return string Returns the file contents
     */
    public function getContent()
    {
        if (is_null($this->content)) {
            $this->content = (string) file_get_contents($this->path);
        }
        return $this->content;
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
        $this->content = $content;
    }

    /**
     * Saves the file to media
     * 
     * @throws \Duality\Core\DualityException When could not save file
     * 
     * @return void
     */
    public function save()
    {
        if ($this->exists()) {
            if (!is_writable(dirname($this->path)) 
                || !is_writable($this->path)
            ) {
                throw new DualityException(
                    "Could not save file: ".$this->getPath(), 4
                );
            }
        } else {
            if (!is_writable(dirname($this->path))) {
                throw new DualityException(
                    "Could not save file: ".$this->getPath(), 4
                );
            }
        }
        file_put_contents($this->getPath(), $this->getContent());
    }
}