<?php

/**
 * Interface for command line operations
 * 
 * PHP Version 5.3.4
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
 * Provides an interface for all Duality command-line services
 * ie. \Duality\Service\Commander
 * 
 * PHP Version 5.3.4
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
     * @param array $server The $_SERVER to be passed
     * 
     * @return string The user input
     */
    public static function parseFromGlobals($server);

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