<?php

/**
 * Array storage
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\InterfaceStorage;

/**
 * Session interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Storage
implements InterfaceStorage
{
    /**
     * Holds the data
     * 
     * @var array Holds the data
     */
    protected $buffer;

    /**
     * Creates a new array storage
     */
    public function __construct()
    {
        $this->buffer = array();
    }

    /**
     * Add item
     * 
     * @param string $key   Give the key to be identified
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function add($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Set item
     * 
     * @param string $key   Give the key to be identified
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function set($key, $value)
    {
        $this->buffer[$key] = $value;
    }

    /**
     * Return item
     * 
     * @param string $key Give the value key
     * 
     * @return mixed The stored value
     */
    public function get($key)
    {
        return $this->has($key) ? $this->buffer[$key] : false;
    }

    /**
     * Checks if item exists
     * 
     * @param string $key Give the value key
     * 
     * @return boolean If exists, return true, false otherwise
     */
    public function has($key)
    {
        return array_key_exists($key, $this->buffer);
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        return (array) $this->buffer;
    }

    /**
     * Loads items into storage
     * 
     * @param array $data The data to be loaded
     * 
     * @return void
     */
    public function importArray($data)
    {
        $this->buffer = (array) $data;
    }

    /**
     * Remove item by its key
     * 
     * @param string $key Give the value key
     * 
     * @return void
     */
    public function remove($key)
    {
        unset($this->buffer[$key]);
    }

    /**
     * Clear storage
     * 
     * @return void
     */
    public function reset()
    {
        $this->buffer = array();
    }

}