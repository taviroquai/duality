<?php

namespace Duality\System\Structure;

class Entity extends Property {
	
	protected $properties;

	protected $defaultIdProperty = 'id';

	public function __construct(Property $id)
	{
		parent::__construct();

		$this->properties = array();
		$this->addProperty(new Property($this->defaultIdProperty));
	}

	public function addProperty(Property $property)
	{
		$this->properties[] = $property;
	}

	public function getProperties()
	{
		return $this->properties;
	}

	public function __toString()
	{
		return $this->getName();
	}

}