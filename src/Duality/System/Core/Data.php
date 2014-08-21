<?php

namespace Duality\System\Core;

/**
 * Holds a data value and a default value
 */
class Data {

    /**
     * The data value
     * @var mixed
     */
	protected $value;

    /**
     * The default value
     * @var null
     */
	protected $default = NULL;
	
    /**
     * Creates a new data object
     */
	public function __construct()
	{
		$this->value = $this->default;
	}

    /**
     * Sets the data value
     * @param mixed $value
     */
	public function setValue($value)
	{
		$this->value = $value;
	}

    /**
     * Gets the data value
     * @return mixed
     */
	public function getValue()
	{
		return $this->value;
	}

    /**
     * Sets the default value
     * @param mixed $value
     */
	public function setDefault($value)
	{
		$this->default = $value;
	}

    /**
     * Gets the default value
     * @return mixed
     */
	public function getDefault()
	{
		return $this->default;
	}

    /**
     * Converts the value to string
     * @return string
     */
	public function __toString()
	{
		return (string) $this->value;
	}

}