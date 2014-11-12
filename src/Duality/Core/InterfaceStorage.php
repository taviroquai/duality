<?php

/**
 * Interface for generic storage
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Session interface
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceStorage
{
    /**
     * Add item
     * 
     * @param string $key   Give the key to be identified
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function add($key, $value);

    /**
     * Set item
     * 
     * @param string $key   Give the key to be identified
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function set($key, $value);

    /**
     * Return item
     * 
     * @param string $key Give the value key
     * 
     * @return mixed The stored value
     */
    public function get($key);

    /**
     * Checks if item exists
     * 
     * @param string $key Give the value key
     * 
     * @return boolean If exists, return true, false otherwise
     */
    public function has($key);

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray();

    /**
     * Loads items into storage
     * 
     * @param array $data The data to be loaded
     * 
     * @return void
     */
    public function importArray($data);

    /**
     * Remove item by its key
     * 
     * @param string $key Give the value key
     * 
     * @return void
     */
    public function remove($key);

    /**
     * Clear storage
     * 
     * @return void
     */
    public function reset();

}