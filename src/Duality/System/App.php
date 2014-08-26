<?php

namespace Duality\System;

use Duality\System\Core\DualityException;
use Duality\System\Core\Container;

/**
 * Default application container
 */
class App extends Container
{
	/**
	 * Container cache
	 * var $array
	 */
	protected $cache;

	/**
	 * Register service
	 * @param string $name
	 * @return Container
	 */
	public function register($name, \Closure $service)
	{
		$this->services[$name] = $service;
		return $this;
	}

	/**
	 * Call service
	 * @param string $name
	 * @return mixed
	 */
	public function call($name)
	{
		if (!isset($this->services[$name])) {
			throw new DualityException("Service undefined: " . $name, 15);
		}
		if (!isset($this->cache[$name])) {
			$this->cache[$name] = call_user_func($this->services[$name]);
		}
		return $this->cache[$name];
	}
}