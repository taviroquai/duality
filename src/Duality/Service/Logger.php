<?php

/**
 * Logger service (file)
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Core\InterfaceErrorHandler;
use Duality\Structure\File\StreamFile;

/**
 * Default logger service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Logger 
extends AbstractService
implements InterfaceErrorHandler
{
    /**
     * Holds the stream that will receive the log
     * 
     * @var \Duality\Structure\File\StreamFile $stream Holds the buffer to store logs
     */
    protected $stream;

    /**
     * Holds if there was any error or not
     * 
     * @var $error Holds a global error flag
     */
    protected $error = false;

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('logger.buffer')) {
            throw new DualityException(
                "Error Config: log_file configuration not found", 1
            );
        }
        $this->stream = new StreamFile($this->app->getConfigItem('logger.buffer'));
        $this->stream->open('a+b');
        set_error_handler(array($this, 'error'));
        set_exception_handler(array($this, 'myException'));
    }

    /**
     * Terminate service
     * 
     * @return void
     */
    public function terminate()
    {
        if ($this->error) {
            echo 'Ops! Something went wrong...';
        }
        if ($this->stream) {
            $this->stream->close();
        }
    }

    /**
     * Log action
     * 
     * @param string $msg Give the message to log
     * 
     * @return void
     */
    public function log($msg)
    {
        trigger_error($msg);
    }

    /**
     * Handles exceptions
     * 
     * @param \Exception $e Give the exception to handler
     * 
     * @return void
     */
    public function myException($e)
    {
        $this->error(E_USER_ERROR, $e->getMessage(), $e->getFile(), $e->getLine());
    }

    /**
     * Default error handler
     * 
     * @param int    $errno   Give the error number
     * @param string $errstr  Give the error description
     * @param string $errfile Give file from which came the error
     * @param int    $errline Give line from which came the error
     * 
     * @return void|boolean
     */
    public function error($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }

        if (!$this->stream) {
            // cannot log anything... break?!?
            return;
        }

        $msg = '';

        switch ($errno) {
        case E_USER_ERROR:
            $msg  = "My Fatal Error [$errno] $errstr"
                . "on line $errline in file $errfile\n";
            $msg .= "PHP ". PHP_VERSION . " (" . PHP_OS . ")\n";
            $msg .= "Cannot continue. Aborting...\n";
            break;

        case E_USER_WARNING:
            $msg  = "My Warning [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            break;

        case E_USER_NOTICE:
            $msg  = "My Notice [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            break;

        default:
            $msg  = "My Notice [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            break;
        }
        
        // Add date to message
        $msg = date('Y-m-d H:i:s').": ".$msg;

        // write log
        $this->stream->write($msg);

        // Set error
        $this->error = true;

        /* Don't execute PHP internal error handler */
        return true;
    }

}