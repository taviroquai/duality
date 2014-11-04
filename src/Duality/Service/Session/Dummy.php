<?php

/**
 * Dummy session service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.19.0
 */

namespace Duality\Service\Session;

use Duality\Structure\Storage;
use Duality\Service\Session;

/**
 * Dummy session service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.19.0
 */
class Dummy
extends Session
{
    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->storage = new Storage;
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        unset($this->storage);
    }

    /**
     * Add item
     * 
     * @param string $key   Give the key which identifies the value
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function add($key, $value)
    {
        $this->storage->add($key, $value);
    }

    /**
     * Save item
     * 
     * @param string $key   Give the key which identifies the value
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function set($key, $value)
    {
        $this->storage->set($key, $value);
    }

    /**
     * Return item
     * 
     * @param string $key Give the key to retrieve the value
     * 
     * @return mixed|null The value to be retrieved or null
     */
    public function get($key)
    {
        return $this->storage->get($key);
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
        return $this->storage->has($key);
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        return $this->storage->asArray();
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
        $this->storage->remove($key);
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
        $this->storage->importArray($data);
    }

    /**
     * Reset a session
     * 
     * @return void
     */
    public function reset()
    {
        $this->storage->reset();
    }

}