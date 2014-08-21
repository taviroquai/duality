<?php

namespace Duality\System\File;

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
		parent::__construct();
		$this->setPath($path);
	}

    /**
     * Opens the stream
     * @param string $options
     * @throws \Exception
     */
	public function open($options = 'w+b')
	{
		$this->handler = @fopen($this->path, $options);
		if (!is_resource($this->handler)) {
			throw new \Exception("Could not open stream: ".$this->getPath(), 5);
		}
	}

    /**
     * Closes the stream
     * @throws \Exception
     */
	public function close()
	{
		if (!is_resource($this->handler)) {
			throw new Exception("Stream not opened: ".$this->getPath(), 6);
		}
		fclose($this->handler);
	}

    /**
     * Sets up the load callback
     * @param \Clousure $callback
     * @throws \Exception
     */
	public function load(\Clousure $callback = null)
	{
		if (!is_resource($this->handler)) {
			throw new \Exception("Stream not opened: ".$this->getPath(), 6);
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
     * @throws Exception
     */
	public function save()
	{
		if (!is_resource($this->handler)) {
			throw new Exception("Stream not opened: ".$this->getPath(), 6);
		}
		rewind($this->handler);
		return fwrite($this->handler, $this->content);
	}
}