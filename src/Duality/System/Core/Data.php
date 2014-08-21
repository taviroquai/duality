<?php

namespace Duality\System\Core;

class Data {

	protected $value;

	protected $default = NULL;
	
	public function __construct()
	{
		$this->value = $this->default;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}


	public function setDefault($value)
	{
		$this->default = $value;
	}

	public function getDefault()
	{
		return $this->default;
	}

	public function __toString()
	{
		return (string) $this->value;
	}

}