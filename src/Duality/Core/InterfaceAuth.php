<?php

/**
 * Interface for authentication
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Authentication interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceAuth
{
    /**
     * Login using a 2-key (username, password)
     * 
     * @param string   $username        Give the username/email parameter
     * @param string   $password        Give the password parameter
     * @param \Closure $storageCallback Give the storage callback
     * 
     * @return boolean Return success or failure
     */
    public function login($username, $password, \Closure $storageCallback);

    /**
     * Check whether there is a user logged
     * 
     * @return boolean Tells whether the user is logged or not
     */
    public function isLogged();

    /**
     * Terminates user session
     * 
     * @return void
     */
    public function logout();

    /**
     * Returns the current logged user
     * 
     * @return string Returns the current logged username
     */
    public function whoAmI();

}