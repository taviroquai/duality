<?php

/**
 * Authentication service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceAuth;
use Duality\System\App;

/**
 * Default authentication service
 */
class Auth 
implements InterfaceAuth, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Default storage
	 * @var mixed
	 */
	protected $storage;

	/**
	 * Holds the current logged username
	 * @var string
	 */
	protected $current;

	/**
	 * Creates a new error handler
	 * @param Duality\System\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		$this->storage = array(array());
		$this->app->register('auth_storage', function() {
			return array();
		});
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{

	}

	/**
	 * Login using a 2-key (username, password)
	 * @param string $username
	 * @param string $password
	 * @param \Closure $storageCallback
	 * @return boolean
	 */
	public function login($username, $password, \Closure $storageCallback)
	{
		if (count($storageCallback($username, $password))) {
			$this->app->call('session')->set('__user', $username);
			$this->current = $username;
			return true;	
		}
		return false;
	}

	/**
	 * Check if there is a user logged
	 * @return boolean
	 */
	public function isLogged()
	{
		$user = $this->app->call('session')->get('__user');
		if (!empty($user)) {
			return true;
		}
		return false;
	}

	/**
	 * Logs a user out
	 * @return boolean
	 */
	public function logout()
	{
		$this->app->call('session')->reset();
	}

	/**
	 * Returns the current logged user
	 * @return mixed
	 */
	public function whoAmI()
	{
		return $this->current;
	}

}