<?php

/**
 * Container class
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Abstract Container for DIC
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Container
{
    /**
     * Container services
     *
     * @var \Duality\Core\InterfaceStorage Holds the services storage
     */
    protected $services;

    /**
     * Register service
     *
     * @param string   $name    Give a name to the service
     * @param \Closure $service Give the service as a \Closure
     * 
     * @return void
     */
    public abstract function register($name, \Closure $service);

    /**
     * Call service
     *
     * @param string $name Give a name to idetify the service
     * 
     * @return \Duality\Core\InterfaceService The service closure
     */
    public abstract function call($name);

    /**
     * Return registered services
     *
     * @return array Returns all services
     */
    public function getServices()
    {
        return (array) $this->services->asArray();
    }
}