<?php

/**
 * Interface for remote operations
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Core;

/**
 * Remote interface
 */
interface InterfaceRemote
{
    /**
     * Starts a new connection
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $port
     */
    public function connect($host, $username, $password = '', $port = 22);
    
    /**
     * Disconnect from server
     */
    public function disconnect();
    
    /**
     * Runs a command on remote server
     * @param string $cmd
     * @return string
     */
    public function execute($cmd);
    
}