<?php

/**
 * Logger service (file)
 *
 * PHP Version 5.3.4
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
 * PHP Version 5.3.4
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
                "Error Config: log_file configuration not found",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        $filename = $this->app->getPath()
            . DIRECTORY_SEPARATOR
            . $this->app->getConfigItem('logger.buffer');
        if (!file_exists($filename)) {
            throw new DualityException(
                "Error Config: invalid log_file:" . $filename,
                DualityException::E_FILE_NOTFOUND
            );
        }
        $this->stream = new StreamFile($filename);
        $this->stream->open('a+b');
        set_error_handler(array($this, 'error'));
    }

    /**
     * Terminate service
     * 
     * @return void
     */
    public function terminate()
    {
        if ($this->error) {
            $this->app->getBuffer()->write('Ops! Something went wrong...');
        }
        if ($this->stream) {
            $this->stream->close();
        }
    }

    /**
     * Log action
     * 
     * @param string $msg        Give the message to log
     * @param int    $error_type Give the type of information to be logged
     * 
     * @return void
     */
    public function log($msg, $error_type = E_USER_NOTICE)
    {
        trigger_error($msg, $error_type);
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
        $msg = '';

        switch ($errno) {
        case E_USER_ERROR:
            $msg  = "Duality Fatal Error [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            $msg .= "PHP ". PHP_VERSION . " (" . PHP_OS . ")\n";
            $msg .= "Cannot continue. Aborting...\n";
            break;

        case E_USER_WARNING:
            $msg  = "Duality Warning [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            break;

        default:
            $msg  = "Duality Notice [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            break;
        }

        // Set error
        $this->error = true;
        
        // Add date to message
        $msg = date('Y-m-d H:i:s').": ".$msg;

        // write log to buffer
        $this->stream->write($msg);

        /* Don't execute PHP internal error handler */
        return true;
    }

}