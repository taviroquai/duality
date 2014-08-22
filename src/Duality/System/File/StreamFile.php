<?php

namespace Duality\System\File;

use Duality\System\Core\DualityException;
use Duality\System\Structure\File;

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
     * @throws \Duality\System\Core\DualityException
     */
	public function open($options = 'w+b')
	{
		$this->handler = @fopen($this->path, $options);
		if (!is_resource($this->handler)) {
			throw new DualityException("Could not open stream: ".$this->getPath(), 5);
		}
	}

    /**
     * Closes the stream
     * @throws \Duality\System\Core\DualityException
     */
	public function close()
	{
		if (!is_resource($this->handler)) {
			throw new DualityException("Stream not opened: ".$this->getPath(), 6);
		}
		fclose($this->handler);
	}

    /**
     * Sets up the load callback
     * @param \Clousure $callback
     * @throws \\Duality\System\Core\DualityException
     */
	public function load(\Clousure $callback = null)
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
     * @throws \Duality\System\Core\DualityException
     */
	public function save()
	{
		if (!is_resource($this->handler)) {
			throw new DualityException("Stream not opened: ".$this->getPath(), 6);
		}
		rewind($this->handler);
		return fwrite($this->handler, $this->content);
	}
}