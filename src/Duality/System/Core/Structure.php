<?php

namespace Duality\System\Core;

/**
 * The structure class
 */
class Structure {

    /**
     * Holds the structure name
     * @var string
     */
	protected $name;
	
    /**
     * Creates the structure instance
     */
	public function __construct()
	{
		
	}

    /**
     * Sets the structure name
     * @param string $name
     */
	public function setName($name)
	{
		$this->name = $name;
	}

    /**
     * Gets the structure name
     * @return string
     */
	public function getName()
	{
		return $this->name;
	}

    /**
     * Gets the structure as string
     * @return string
     */
	public function __toString()
	{
		return $this->name;
	}

}