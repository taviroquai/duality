<?php

/**
 * SSH authentication service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Service\Auth;

use Duality\Core\DualityException;
use Duality\Core\AbstractAuth;

/**
 * SSH authentication service
 * 
 * Provides operations for dealing with remote executions
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
class SSH
extends AbstractAuth
{
    /**
     * Holds the remote host
     * 
     * @var string
     */
    protected $host;
    
    /**
     * Holds the remote port
     * 
     * @var integer
     */
    protected $port;
    
    /**
     * Own public key
     * 
     * @var string Holds the public key to authenticate
     */
    protected $ssh_auth_pub = '/home/%s/.ssh/id_rsa.pub';
    
    /**
     * Own private key
     * 
     * @var string Holds the private key to authenticate
     */
    protected $ssh_auth_priv = '/home/%s/.ssh/id_rsa';

    /**
     * Paraphrase (empty == null)
     * 
     * @var string Holds the authentication paraphrase
     */
    protected $ssh_auth_pass;
    
    /**
     * Remote fingerprint (empty == null)
     * 
     * @var string Holds the remote server fingerprint
     */
    protected $ssh_fingerprint;

    /**
     * SSH Connection
     * 
     * @var \resource Holds the connection resource
     */
    protected $connection;

    /**
     * On end disconnect
     * 
     * @return void
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('auth.ssh.host')) {
            throw new DualityException(
                "Error Config: auth.ssh.host configuration not found",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        
        // Start connection
        $this->host = $this->app->getConfigItem('auth.ssh.host');
        $this->port = $this->app->getConfigItem('auth.ssh.port') ?
                $this->app->getConfigItem('auth.ssh.port') : 22;
        $this->ssh_auth_pass = $this->app->getConfigItem('auth.ssh.paraphrase');
        $this->ssh_fingerprint = $this->app->getConfigItem('auth.ssh.fingerprint');
        if (!($this->connection = @ssh2_connect($this->host, $this->port))) {
            throw new DualityException(
                'Cannot connect to auth host',
                DualityException::E_REMOTE_NOTCONNECTED
            );
        }
    }
    
    /**
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password)
    {
        $this->checkParaphrase();
        return $this->status = @ssh2_auth_password($this->connection, $username, $password);
    }
    
    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        $this->disconnect();
    }

    /**
     * Disconnect from server
     * 
     * @return void
     */
    public function disconnect()
    {
        $this->connection = null;
    }
    
    /**
     * Gets the ssh connection
     * 
     * @return \resource The SSH connection resource
     */
    public function getConnection()
    {
        return $this->connection;
    }
    
    protected function checkParaphrase()
    {
        // Verify fingerprint
        $fingerprint = @ssh2_fingerprint(
            $this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX
        );
        if (!empty($this->ssh_fingerprint)
            && (strcmp($this->ssh_fingerprint, $fingerprint) !== 0)
        ) {
            throw new DualityException(
                'Unable to verify ssh server identity!',
                DualityException::E_REMOTE_FINGERPRINTNOTFOUND
            );
        }
    }

}