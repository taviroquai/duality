<?php

/**
 * Interface performance
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.15.1
 */

namespace Duality\Core;

/**
 * Interface performance
 * 
 * Provides an interface for performance operations
 * ie. \Duality\Service\Performance
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.15.1
 */
interface InterfacePerformance
{
    /**
     * Add checkpoint
     * 
     * @param string $key Give the key which identifies the value
     * 
     * @return void
     */
    public function checkpoint($key);

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray();

    /**
     * Return current performance time
     * 
     * @return float Return current time as 0.000
     */
    public function getCurrentTime();

    /**
     * Reset a session
     * 
     * @return void
     */
    public function reset();

}