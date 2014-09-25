<?php

/**
 * Container class
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

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
	 * @param \Closure $service
	 */
	public abstract function register($name, \Closure $service);

	/**
	 * Call service
	 * @param string $name
	 */
	public abstract function call($name);
    
    /**
     * Return registered services
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

}