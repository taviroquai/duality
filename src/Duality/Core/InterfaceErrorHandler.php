<?php

/**
 * Interface for error handler
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
 * Default error handler
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceErrorHandler
{
    /**
     * Default error action
     * 
     * @param int    $errno   The error code
     * @param string $errstr  The error description
     * @param string $errfile The file name where the error occured
     * @param int    $errline The line where the error occured
     * 
     * @return void
     */
    public function error($errno, $errstr, $errfile, $errline);

    /**
     * Log action
     * 
     * @param string $msg Give the complete log message
     * 
     * @return void
     */
    public function log($msg);

    /**
     * Terminates error handler
     * 
     * @return void
     */
    public function terminate();
}