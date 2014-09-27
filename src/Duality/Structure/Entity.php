<?php

/**
 * Abstract entity structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Structure;

use \Duality\Core\DualityException;

/**
 * Entity class
 */
abstract class Entity 
extends Property
{	
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
     */
	public function __construct()
	{
		parent::__construct();

		$this->properties = array();
		$this->addProperty(new Property($this->defaultIdProperty));
	}

    /**
     * Adds a property to the entity
     * @param \Duality\Structure\Property $property
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
     * Adds properties from a list of properties
     * @param array $array
     */
    public function addPropertiesFromArray($array)
    {
        if (!is_array($array) || count($array) == 0) {
            throw new DualityException("Array of properties cannot be empty", 12);
        }
        foreach ($array as $name) {
            $property = new Property($name);
            $this->addProperty($property);
        }
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