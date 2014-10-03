<?php

/**
 * Cache service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\InterfaceService;
use Duality\Core\InterfaceStorage;
use Duality\App;

/**
 * Default cache service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Cache 
implements InterfaceStorage, InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var Duality\App The application container
     */
    protected $app;

    /**
     * Creates a new error handler
     * 
     * @param Duality\App &$app The application container
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
        $value = null;
        if (apc_exists($key)) {
            $value = apc_fetch($key);
            apc_delete($key);
        }
        return null;
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
        $this->put($key, $value)
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
        return apc_exists($key) ? apc_fetch($key) : null;
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