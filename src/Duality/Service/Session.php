<?php

/**
 * User session service (native php sessions)
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\InterfaceService;
use Duality\Core\InterfaceStorage;
use Duality\App;

/**
 * Default session service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Session 
implements InterfaceStorage, InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var Duality\App Holds the application container
     */
    protected $app;

    /**
     * Creates a new error handler
     * 
     * @param \Duality\App &$app Give the application container
     */
    public function __construct(App &$app)
    {
        $this->app = & $app;
    }

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
        if (session_id() == '') {
            session_write_close();
        }
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