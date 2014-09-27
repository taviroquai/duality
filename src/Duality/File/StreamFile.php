<?php

/**
 * Stream file structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\File;

use Duality\Core\DualityException;
use Duality\Structure\File;

/**
 * The stream class
 */
class StreamFile extends File {

    /**
     * Stream resource handler
     * @var resource
     */
	protected $handler;

    /**
     * Holds the stream file path
     * @param string $path
     */
	public function __construct($path)
	{
		$this->setPath($path);
	}

    /**
     * Opens the stream
     * @param string $options
     * @throws \Duality\Core\DualityException
     */
	public function open($options = 'w+b')
	{
        if (!is_resource($this->handler)) {
            $this->handler = @fopen($this->path, $options);
            if (!is_resource($this->handler)) {
                throw new DualityException("Could not open stream: ".$this->getPath(), 5);
            }
        }
	}

    /**
     * Closes the stream
     * @throws \Duality\Core\DualityException
     */
	public function close()
	{
		if (is_resource($this->handler)) {
			fclose($this->handler);
		}
	}

    /**
     * Sets up the load callback
     * @param \Closure $callback
     * @throws \\Duality\Core\DualityException
     */
	public function load(\Closure $callback = null)
	{
		if (!is_resource($this->handler)) {
			throw new DualityException("Stream not opened: ".$this->getPath(), 6);
		}
		rewind($this->handler);
		while ($chunck = fread($this->handler, 4096)) {
			$this->content .= $chunck;
			if (!is_null($callback)) {
				$callback($chunck);
			}
		}
	}

    /**
     * Saves the full file stream
     * @return boolean
     * @throws \Duality\Core\DualityException
     */
	public function save()
	{
		if (!is_resource($this->handler)) {
			$this->open();
		}
		rewind($this->handler);
		return fwrite($this->handler, $this->content);
	}

	/**
     * Quick write content to file
     * @param string $data
     * @return boolean
     * @throws \Duality\Core\DualityException
     */
	public function write($data)
	{
		if (!is_resource($this->handler)) {
			$this->open();
		}
		return fwrite($this->handler, $data);
	}
}