<?php

/**
 * Authentication interface
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Core;

/**
 * Authentication interface
 * 
 * Provides base functionality for Duality auth services
 * ie. \Duality\Service\Auth\Database
 * ie. \Duality\Service\Auth\LDAP
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
interface InterfaceAuthentication
{
    /**
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password);

    /**
     * Check if there is a user logged
     * 
     * @return boolean Tells whether the user is logged or not
     */
    public function isLogged();

    /**
     * Logs a user out
     * 
     * @return void
     */
    public function logout();

}