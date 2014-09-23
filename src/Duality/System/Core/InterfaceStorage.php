<?php

namespace Duality\System\Core;

/**
 * Session interface
 */
interface InterfaceStorage
{
	/**
	 * Save item
	 * @param string $key
	 * @param string $value
	 * @return boolean
	 */
	public function set($key, $value);

	/**
	 * Return item
	 * @param string $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Restart a session
	 * @return boolean
	 */
	public function reset();

}