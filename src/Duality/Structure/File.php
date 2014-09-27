<?php

/**
 * Abstract file structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Structure;

use Duality\Core\DualityException;
use Duality\Core\Structure;

/**
 * File structure class
 */
abstract class File 
extends Structure
{
    /**
     * Holds the file system path
     * @var string
     */
	protected $path;

    /**
     * Holds the file meta attributes
     * @var array
     */
	protected $attributes;

    /**
     * Holds the file content
     * @var string
     */
	protected $content;

    /**
     * Sets the file path
     * @param string $path
     */
	public function setPath($path)
	{
		$this->path = $path;
	}

    /**
     * Gets the file path
     * @return string
     */
	public function getPath()
	{
		return $this->path;
	}

    /**
     * Checks whether file exists or not
     * @return boolean
     */
	public function exists()
	{
		return file_exists($this->path);
	}

    /**
     * Loads file meta attributes
     */
	public function loadAttributes()
	{
		$this->attributes = file($this->path);
	}

    /**
     * Loads file contents
     * @return string
     */
	public function getContent()
	{
		if (is_null($this->content)) {
			$this->content = file_get_contents($this->path);
		}
		return $this->content;
	}

    /**
     * Sets the file contents
     * @param string $content
     */
	public function setContent($content)
	{
		$this->content = $content;
	}

    /**
     * Saves the file to media
     * @throws \\Duality\Core\DualityException
     */
	public function save()
	{
		if ($this->exists()) {
			if (!is_writable(dirname($this->path)) || !is_writable($this->path)) {
				throw new DualityException("Could not save file: ".$this->getPath(), 4);
			}
		} else {
			if (!is_writable(dirname($this->path))) {
				throw new DualityException("Could not save file: ".$this->getPath(), 4);
			}
		}
		file_put_contents($this->getPath(), $this->getContent());
	}

}