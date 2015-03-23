<?php

/**
 * SSH service for remote operations
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Service\Auth\SSH as AuthSSH;

/**
 * SSH service for remote operations
 * 
 * Provides operations for dealing with remote executions
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class SSH
extends AbstractService
{
    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        
    }
    
    /**
     * Runs a command on remote server
     * 
     * @param string $cmd The command to execute
     * 
     * @return string The resulting output
     */
    public function execute($cmd)
    {
        $auth = $this->app->call('auth');
        if (!($auth instanceof AuthSSH)
            || !is_resource($auth->getConnection())
            || !($stream = @ssh2_exec($this->app->call('auth')->getConnection(), $cmd))
        ) {
            throw new DualityException(
                'Could not authenticate on ssh',
                DualityException::E_REMOTE_AUTHFAILED
            );
        }
        
        // OK!
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream, 4096)) {
            $data .= $buf;
        }
        fclose($stream);
        return $data;
    }
}