<?php

/**
 * Interface for remote operations
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
 * Remote interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceRemote
{
    /**
     * Starts a new connection
     * 
     * @param string $host     The remote hostname or IP address
     * @param string $username The username for authentication
     * @param string $password The password for authentication
     * @param string $port     The allowed remote network port
     *
     * @return void
     */
    public function connect($host, $username, $password = '', $port = 22);
    
    /**
     * Disconnect from server
     * 
     * @return void
     */
    public function disconnect();
    
    /**
     * Runs a command on remote server
     * 
     * @param string $cmd The command to be executed
     * 
     * @return string The command output
     */
    public function execute($cmd);
    
}