<?php

/**
 * User session service (native php sessions)
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Service;

use Duality\Core\InterfaceService;
use Duality\Core\InterfaceStorage;
use Duality\App;

/**
 * Default session service
 */
class Session 
implements InterfaceStorage, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\App
	 */
	protected $app;

	/**
	 * Holds the session storage
	 * @var array
	 */
	protected $storage;

	/**
	 * Creates a new error handler
	 * @param Duality\App $app
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
		session_start();
		$this->storage = & $_SESSION;
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{
		if (session_id() == '') {
			session_write_close();
		}
	}

	/**
	 * Save item
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value)
	{
		$this->storage[$key] = $value;
	}

	/**
	 * Return item
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->storage[$key]) ? $this->storage[$key] : NULL;
	}

	/**
	 * Reset a session
	 * @return boolean
	 */
	public function reset()
	{
		session_destroy();
		session_start();
		return true;
	}

}