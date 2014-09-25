<?php

/**
 * Cache service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\DualityException;
use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceStorage;
use Duality\System\App;

/**
 * Default cache service
 */
class Cache 
implements InterfaceStorage, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Creates a new error handler
	 * @param Duality\System\App $app
	 */
	public function __construct(App &$app)
	{
		$this->app = & $app;
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		if (!extension_loaded('apcu') && !extension_loaded('apc')) {
			throw new DualityException("Error: apc extension not loaded", 1);
		}
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{

	}

	/**
	 * Save item
	 * @param string $key
	 * @param string $value
	 * @return boolean
	 */
	public function set($key, $value)
	{
		apc_store($key, $value);
	}

	/**
	 * Return item
	 * @return mixed
	 */
	public function get($key)
	{
		return apc_exists($key) ? apc_fetch($key) : NULL;
	}

	/**
	 * Reset a session
	 * @return boolean
	 */
	public function reset()
	{
		apc_clear_cache();
		return true;
	}
}