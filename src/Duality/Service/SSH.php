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
use Duality\Core\InterfaceRemote;

/**
 * Class SSH for remote operations
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
implements InterfaceRemote
{
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
     * Starts a new connection
     * 
     * @param string $host       Give the remote hostname or IP
     * @param string $username   Give the authentication username
     * @param string $password   Give the authentication password
     * @param string $port       Give the network port
     * @param string $paraphrase Give the authentication paraphrase
     * @param string $remote_fp  Give the remote server fingerprint
     * 
     * @return void
     */
    public function connectSSH(
        $host,
        $username,
        $password = '',
        $port = 22,
        $paraphrase = null,
        $remote_fp = ''
    ) {
        $this->ssh_auth_pass = $paraphrase;
        $this->ssh_fingerprint = $remote_fp;
        $this->connect($host, $username, $password, $port);
    }

    /**
     * Starts a new connection
     * 
     * @param string $host     Give the remote hostname or IP address
     * @param string $username Give the authentication username
     * @param string $password Give the authentication password
     * @param string $port     Give the network port
     * 
     * @return void
     */
    public function connect($host, $username, $password = '', $port = 22)
    {
        // Start connection
        if (!($this->connection = @ssh2_connect($host, $port))) {
            throw new DualityException('Cannot connect to server');
        }
        
        // Verify fingerprint
        $fingerprint = @ssh2_fingerprint(
            $this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX
        );
        if (!empty($this->ssh_fingerprint)
            && (strcmp($this->ssh_fingerprint, $fingerprint) !== 0)
        ) {
            throw new DualityException('Unable to verify server identity!');
        }
        
        // Try auth methods
        if (!empty($password)) {
            if (!@ssh2_auth_password($this->connection, $username, $password)) {
                throw new DualityException('Autentication rejected by server');
            }
        } else {
            $public_key_path = sprintf($this->ssh_auth_pub, $username);
            $private_key_path = sprintf($this->ssh_auth_priv, $username);
            if (!ssh2_auth_pubkey_file(
                $this->connection,
                $username,
                $public_key_path,
                $private_key_path,
                $this->ssh_auth_pass
            )) {
                throw new DualityException('Autentication rejected by server');
            }    
        }
    }

    /**
     * Runs a command on remote server
     * 
     * @param string $cmd Give the user command to execute
     * 
     * @return string The resulting output
     */
    public function execute($cmd)
    {
        if (!$this->connection) {
            return '';
        }
        if (!($stream = @ssh2_exec($this->connection, $cmd))) {
            throw new DualityException('SSH command failed');
        }
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream, 4096)) {
            $data .= $buf;
        }
        fclose($stream);
        return $data;
    }

    /**
     * Disconnect from server
     * 
     * @return void
     */
    public function disconnect()
    {
        $this->execute('echo "EXITING" && exit;');
        $this->connection = null;
    }

}