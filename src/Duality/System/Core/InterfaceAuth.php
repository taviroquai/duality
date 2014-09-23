<?php

namespace Duality\System\Core;

/**
 * Authentication interface
 */
interface InterfaceAuth
{
	/**
	 * Login using a 2-key (username, password)
	 * @param string $username
	 * @param string $password
	 * @param \Closure $storageCallback
	 * @return boolean
	 */
	public function login($username, $password, \Closure $storageCallback);

	/**
	 * Check if there is a user logged
	 * @return boolean
	 */
	public function isLogged();

	/**
	 * Logs a user out
	 * @return boolean
	 */
	public function logout();

	/**
	 * Returns the current logged user
	 * @return mixed
	 */
	public function whoAmI();

}