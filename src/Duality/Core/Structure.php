<?php

/**
 * Generic structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * The structure class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Structure
{
    /**
     * Holds the structure name
     * 
     * @var string Holds the structure name
     */
    protected $name;

    /**
     * Sets the structure name
     * 
     * @param string $name Give the name to the structure
     * 
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the structure name
     * 
     * @return string Returns the structure name
     */
    public function getName()
    {
        return (string) $this->name;
    }

    /**
     * Gets the structure as string
     * 
     * @return string The string representation of the structure
     */
    public function __toString()
    {
        return (string) $this->name;
    }

}