<?php

namespace Duality\System\Core;

/**
 * Abstract Container for DIC
 */
abstract class Container
{
	/**
	 * Container services
	 * @var array
	 */
	protected $services;

	/**
	 * Register service
	 * @param string $name
	 */
	public abstract function register($name, \Closure $service);

	/**
	 * Call service
	 * @param string $name
	 */
	public abstract function call($name);

}