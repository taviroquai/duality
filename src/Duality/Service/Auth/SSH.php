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
use Duality\Structure\Storage;

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
     * Holds the ssh configuration
     * 
     * var \Duality\Structure\Storage The configuration params
     */
    protected $config;
    
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
        
        $this->config = new Storage;
        $this->setConfig(
            $this->app->getConfigItem('auth.ssh.host'),
            $this->app->getConfigItem('auth.ssh.port'),
            $this->app->getConfigItem('auth.ssh.paraphrase'),
            $this->app->getConfigItem('auth.ssh.fingerprint')
        );
        
        // Start connection
        if (!($this->connection = @ssh2_connect(
            $this->config->get('host'),
            $this->config->get('port')
            )
        )) {
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
        if (!empty($this->config->get('fingerprint'))
            && (strcmp($this->config->get('fingerprint'), $fingerprint) !== 0)
        ) {
            throw new DualityException(
                'Unable to verify ssh server identity!',
                DualityException::E_REMOTE_FINGERPRINTNOTFOUND
            );
        }
    }
    
    /**
     * Sets the SSh configuration
     * 
     * @param string $host          The remote server address
     * @param string $port          The remote service port
     * @param string $paraphrase    The user paraphrase
     * @param string $fingerprint   The server fingerprint
     * 
     * @return void
     */
    public function setConfig($host, $port = 22, $paraphrase = null, $fingerprint = null)
    {
        $this->config->set('host', $host);
        $this->config->set('port', empty($port) ? 22 : $port);
        $this->config->set('paraphrase', $paraphrase);
        $this->config->set('fingerprint', $fingerprint);
    }

}