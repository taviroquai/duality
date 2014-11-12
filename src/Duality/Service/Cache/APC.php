<?php

/**
 * APC cache service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service\Cache;

use Duality\Core\DualityException;
use Duality\Service\Cache;

/**
 * APC cache service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class APC
extends Cache
{
    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!extension_loaded('apcu') && !extension_loaded('apc')) {
            throw new DualityException("Error: apc extension not loaded", 1);
        }
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {

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
     * Save item
     * 
     * @param string $key    Give the key to identify the value
     * @param string $value  Give the value to be stored
     * @param int    $expire Give the time to live value as integer
     * 
     * @return void
     */
    public function put($key, $value, $expire = 0)
    {
        apc_store($key, $value, $expire);
    }

    /**
     * Return item
     * 
     * @param string $key Give the key to retrieve the value
     * 
     * @return mixed The value to be retrived or null
     */
    public function pull($key)
    {
        $value = $this->get($key);
        $this->remove($key);
        return $value;
    }

    /**
     * Save item
     * 
     * @param string $key   Give the key to identify the value
     * @param string $value Give the value to be stored
     * 
     * @return void
     */
    public function set($key, $value)
    {
        $this->put($key, $value);
    }

    /**
     * Return item
     * 
     * @param string $key Give the key to retrieve the value
     * 
     * @return mixed The value to be retrived or null
     */
    public function get($key)
    {
        return $this->has($key) ? apc_fetch($key) : null;
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
        return apc_exists($key);
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        $items = array();
        $iterator = new \APCIterator('user');
        foreach ($iterator as $item) {
            $items[$item['key']] = $item['value'];
        }
        return $items;
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
        foreach ($data as $key => $value) {
            $this->add($key, $value);
        }
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
        apc_delete($key);
    }

    /**
     * Reset a session
     * 
     * @return void
     */
    public function reset()
    {
        apc_clear_cache();
    }
}