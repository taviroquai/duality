<?php

/**
 * Authentication service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceAuth;

/**
 * Default authentication service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Auth 
extends AbstractService
implements InterfaceAuth
{
    /**
     * Holds the session key
     * 
     * @var string The session auth key
     */
    protected $sessionKey = '__auth';
        
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
     * @param string   $username        The authentication username
     * @param string   $password        The authentication password
     * @param \Closure $storageCallback The storage callback
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password, \Closure $storageCallback)
    {
        if (count($storageCallback($username, $password))) {
            $this->app->call('session')->set($this->sessionKey, $username);
            return true;    
        }
        return false;
    }

    /**
     * Check if there is a user logged
     * 
     * @return boolean Tells whether the user is logged or not
     */
    public function isLogged()
    {
        return $this->app->call('session')->has($this->sessionKey);
    }

    /**
     * Logs a user out
     * 
     * @return void
     */
    public function logout()
    {
        $this->app->call('session')->reset();
    }

    /**
     * Returns the current logged user
     * 
     * @return string The current logged username
     */
    public function whoAmI()
    {
        return $this->app->call('session')->get($this->sessionKey);
    }

}