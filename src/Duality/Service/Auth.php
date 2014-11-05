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
abstract class Auth 
extends AbstractService
{
    /**
     * Holds the session key
     * 
     * @var string The session auth key
     */
    protected $sessionKey = '__auth';

    /**
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    abstract public function login($username, $password);

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