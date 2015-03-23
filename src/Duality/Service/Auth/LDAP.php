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
        if (!$this->app->getConfigItem('auth.host')) {
            throw new DualityException(
                "Error Config: ldap host configuration not found",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        $this->handler = @ldap_connect($this->app->getConfigItem('auth.host'));
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
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password)
    {
        return $this->status = @ldap_bind($this->handler, $username, $password);
    }
}