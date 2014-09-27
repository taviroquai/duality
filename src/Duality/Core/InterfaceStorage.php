<?php

/**
 * Interface for generic storage
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

namespace Duality\Core;

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
	 * Clear storage
	 * @return boolean
	 */
	public function reset();

}