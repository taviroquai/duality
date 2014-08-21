<?php

namespace Duality\System\Core;

class Structure {

	protected $name;
	
	public function __construct()
	{
		
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function __toString()
	{
		return $this->name;
	}

}