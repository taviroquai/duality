<?php

/**
 * Native PHP session service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service\Session;

use Duality\Structure\Storage;
use Duality\Service\Session;

/**
 * Default session service
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Native
extends Session
{
    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        session_start();
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        if (session_id() !== '') {
            session_write_close();
        }
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
        $this->set($key, $value);
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
        $_SESSION[$key] = $value;
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
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
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
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        return (array) $_SESSION;
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
        unset($_SESSION[$key]);
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
        $_SESSION = (array) $data;
    }

    /**
     * Reset a session
     * 
     * @return void
     */
    public function reset()
    {
        session_destroy();
        session_start();
    }

}