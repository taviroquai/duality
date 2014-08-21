<?php

namespace Duality\System\Structure;

use Duality\System\Core\Structure;

class File extends Structure {

	protected $path;

	protected $attributes;

	protected $content;

	public function __construct()
	{
		
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function exists()
	{
		return file_exists($this->path);
	}

	public function loadAttributes()
	{
		$this->attributes = file($this->path);
	}

	public function getContent()
	{
		if (is_null($this->content)) {
			$this->content = file_get_contents($this->path);
		}
		return $this->content;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function save()
	{
		if ($this->exists()) {
			if (!is_writable(dirname($this->path)) || !is_writable($this->path)) {
				throw new \Exception("Could not save file: ".$this->getPath(), 4);
			}
		} else {
			if (!is_writable(dirname($this->path))) {
				throw new \Exception("Could not save file: ".$this->getPath(), 4);
			}
		}
		file_put_contents($this->getPath(), $this->getContent());
	}

}