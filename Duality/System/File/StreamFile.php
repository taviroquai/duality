<?php

namespace Duality\System\File;

use Duality\System\Structure\File;

class StreamFile extends File {

	protected $handler;

	public function __construct($path)
	{
		parent::__construct();
		$this->setPath($path);
	}

	public function open($options = 'w+b')
	{
		$this->handler = @fopen($this->path, $options);
		if (!is_resource($this->handler)) {
			throw new Exception("Could not open stream: ".$this->getPath(), 5);
		}
	}

	public function close()
	{
		if (!is_resource($this->handler)) {
			throw new Exception("Stream not opened: ".$this->getPath(), 6);
		}
		fclose($this->handler);
	}

	public function load(\Clousure $callback = null)
	{
		if (!is_resource($this->handler)) {
			throw new Exception("Stream not opened: ".$this->getPath(), 6);
		}
		rewind($this->handler);
		while ($chunck = fread($this->handler, 4096)) {
			$this->content .= $chunck;
			if (!is_null($callback)) {
				$callback($chunk);
			}
		}
	}

	public function save()
	{
		if (!is_resource($this->handler)) {
			throw new Exception("Stream not opened: ".$this->getPath(), 6);
		}
		rewind($this->handler);
		$result = fwrite($this->handler, $this->content);
	}
}