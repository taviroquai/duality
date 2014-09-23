<?php

namespace Duality\System\Service;

use Duality\System\Core\DualityException;
use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceRemote;
use Duality\System\App;

class SSH
implements InterfaceService, InterfaceRemote
{
    /**
     * The dependent application container
     * @var Duality\System\App
     */
    protected $app;

    /**
     * Own public key
     * @var string
     */
    protected $ssh_auth_pub = '/home/%s/.ssh/id_rsa.pub';
    
    /**
     * Own private key
     * @var string
     */
    protected $ssh_auth_priv = '/home/%s/.ssh/id_rsa';

    /**
     * Paraphrase (empty == null)
     * @var string
     */
    protected $ssh_auth_pass;
    
    /**
     * Remote fingerprint (empty == null)
     * @var string
     */
    protected $ssh_fingerprint;

    /**
     * SSH Connection
     * @var \resource
     */
    protected $connection;

    /**
     * Creates a new error handler
     * @param Duality\System\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Initiates the service
     */
    public function init()
    {
        
    }

    /**
     * Terminates the service
     */
    public function terminate()
    {
        $this->disconnect();
    }
    
    /**
     * Starts a new connection
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $port
     * @param string $paraphrase
     * @param string $remote_fp
     */
    public function connectSSH($host, $username, $password = '', $port = 22, $paraphrase = NULL, $remote_fp = '')
    {
        $this->ssh_auth_pass = $paraphrase;
        $this->ssh_fingerprint = $remote_fp;
        $this->connect($host, $username, $password, $port);
    }

    /**
     * Starts a new connection
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $port
     */
    public function connect($host, $username, $password = '', $port = 22) {

        // Start connection
        if (!($this->connection = ssh2_connect($host, $port))) {
            throw new DualityException('Cannot connect to server');
        }
        
        // Verify fingerprint
        if (!empty($this->ssh_fingerprint)) {
            $fingerprint = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
            if (strcmp($this->ssh_fingerprint, $fingerprint) !== 0) {
                throw new DualityException('Unable to verify server identity!');
            }
        }
        
        // Try auth methods
        if (!empty($password)) {
            if (!ssh2_auth_password($this->connection, $username, $password)) {
                throw new DualityException('Autentication rejected by server');
            }
        } else {
            $public_key_path = sprintf($this->ssh_auth_pub, $username);
            $private_key_path = sprintf($this->ssh_auth_priv, $username);
            if (!ssh2_auth_pubkey_file($this->connection, $username, $public_key_path, $private_key_path, $this->ssh_auth_pass)) {
                throw new DualityException('Autentication rejected by server');
            }    
        }
    }

    /**
     * Runs a command on remote server
     * @param string $cmd
     * @return string
     */
    public function execute($cmd) {
        if (!$this->connection) {
            return '';
        }
        if (!($stream = ssh2_exec($this->connection, $cmd))) {
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
     */
    public function disconnect() {
        $this->execute('echo "EXITING" && exit;');
        $this->connection = null;
    }

    /**
     * On end disconnect
     */
    public function __destruct() {
        $this->disconnect();
    }
}