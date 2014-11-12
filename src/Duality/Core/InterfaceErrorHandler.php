<?php

/**
 * Interface for error handler
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Default error handler
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceErrorHandler
{
    /**
     * Log action
     * 
     * @param string $msg        Give the complete log message
     * @param int    $error_type Give the type of information to be logged
     * 
     * @return void
     */
    public function log($msg, $error_type = E_USER_NOTICE);
}