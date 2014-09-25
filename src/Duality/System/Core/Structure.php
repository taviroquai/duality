<?php

/**
 * Generic structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Core;

/**
 * The structure class
 */
abstract class Structure {

    /**
     * Holds the structure name
     * @var string
     */
	protected $name;

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