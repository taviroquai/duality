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

use Duality\Core\InterfaceService;
use Duality\Core\InterfaceAuth;
use Duality\App;

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
implements InterfaceAuth, InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var \Duality\App The application container
     */
    protected $app;

    /**
     * Holds the current logged username
     * 
     * @var string The current user logged in
     */
    protected $current;

    /**
     * Creates a new error handler
     * 
     * @param Duality\App $app The application container
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->current = '';
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
            $this->app->call('session')->set('__user', $username);
            $this->current = $username;
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
        $user = $this->app->call('session')->get('__user');
        if (!empty($user)) {
            return true;
        }
        return false;
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
        return $this->current;
    }

}