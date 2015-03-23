<?php

/**
 * LDAP authentication service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */

namespace Duality\Service\Auth;

use Duality\Core\DualityException;
use Duality\Core\AbstractAuth;
use Duality\Structure\Storage;

/**
 * Default authentication service
 * 
 * Provides operations to authenticate against an LDAP server
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */
class LDAP
extends AbstractAuth
{
    /**
     * Holds the ldap configuration
     * 
     * var \Duality\Structure\Storage The configuration params
     */
    protected $config;
    
    /**
     * The ldap connection resource
     * 
     * var resource The connection handler
     */
    protected $handler;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('auth.ldap.host')) {
            throw new DualityException(
                "Error Config: auth.ldap.host configuration not found",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        
        $this->config = new Storage;
        $this->setConfig($this->app->getConfigItem('auth.ldap.host'));
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        @ldap_unbind($this->handler);
    }
    
    /**
     * Connects to LDAP server
     */
    public function connect()
    {
        $this->handler = @ldap_connect($this->config->get('host'));
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
        if (!$this->handler) {
            $this->connect();
        }
        return $this->status = @ldap_bind($this->handler, $username, $password);
    }
    
    /**
     * Sets the LDAP configuration
     * 
     * @param string $host The ldap host address
     */
    public function setConfig($host)
    {
        $this->config->set('host', $host);
    }
}