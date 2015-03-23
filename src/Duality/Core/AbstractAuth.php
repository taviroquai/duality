<?php

/**
 * Dummy authentication service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */

namespace Duality\Core;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceAuthentication;

/**
 * Dummy authentication service
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
abstract class AbstractAuth
extends AbstractService
implements InterfaceAuthentication
{   
    /**
     * Holds the login status
     * 
     * @var boolean The login status
     */
    protected $status;

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
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    public abstract function login($username, $password);
    
    /**
     * Check if there is a user logged
     * 
     * @return boolean Tells whether the user is logged or not
     */
    public function isLogged()
    {
        return $this->status;
    }

    /**
     * Logs a user out
     * 
     * @return void
     */
    public function logout()
    {
        $this->status = false;
    }
}