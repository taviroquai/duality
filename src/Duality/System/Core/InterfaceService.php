<?php

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