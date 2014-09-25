<?php

/**
 * Interface for generic service
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

namespace Duality\System\Core;

/**
 * Starts and ends a service
 */
interface InterfaceService
{
	/**
	 * Initiates the service
	 */
	public function init();

	/**
	 * Terminates the service
	 */
	public function terminate();
}