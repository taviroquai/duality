<?php

/**
 * Interface for command line operations
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
 * Commander interface
 * 
 * PHP Version 5.3.3
 * 
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceCommander
{
    /**
     * Executes commander responders
     * 
     * @return void
     */
    public function listen();

    /**
     * Parses the command input
     *
     * @return void
     */
    public static function parseFromGlobals();

    /**
     * Adds command responder
     * 
     * @param string   $uriPattern Give a regex pattern to be match user input
     * @param \Closure $cb         Give the responder closure
     * 
     * @return void
     */
    public function addResponder($uriPattern, $cb);
}