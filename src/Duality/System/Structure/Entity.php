<?php

namespace Duality\System\Structure;

/**
 * Entity class
 */
class Entity extends Property {
	
    /**
     * Holds the entity properties
     * @var array
     */
	protected $properties;

    /**
     * Holds the special ID property
     * @var string
     */
	protected $defaultIdProperty = 'id';

    /**
     * Creates a new entity
     * @param \Duality\System\Structure\Property $id
     */
	public function __construct(Property $id)
	{
		parent::__construct();

		$this->properties = array();
		$this->addProperty(new Property($this->defaultIdProperty));
	}

    /**
     * Adds a property to the entity
     * @param \Duality\System\Structure\Property $property
     */
	public function addProperty(Property $property)
	{
		$this->properties[] = $property;
	}

    /**
     * Gets all entity's properties
     * @return array
     */
	public function getProperties()
	{
		return $this->properties;
	}

    /**
     * Returns the entity name
     * @return string
     */
	public function __toString()
	{
		return $this->getName();
	}

}