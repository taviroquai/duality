<?php

/**
 * Logger service (file)
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Duality\Structure\File\StreamFile;

/**
 * Logger service
 * 
 * Provides basic functionality for logger services
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
class Logger 
extends AbstractService
implements LoggerInterface
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
        set_error_handler(array($this, 'error_handler'));
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
     * Default error handler
     * 
     * @param int    $errno   Give the error number
     * @param string $errstr  Give the error description
     * @param string $errfile Give file from which came the error
     * @param int    $errline Give line from which came the error
     * 
     * @return void|boolean
     */
    public function error_handler($errno, $errstr, $errfile, $errline)
    {
        $message = '';
        $level = LogLevel::CRITICAL;

        switch ($errno) {
        case E_USER_ERROR:
            $message  = "Duality Fatal Error [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            $message .= "PHP ". PHP_VERSION . " (" . PHP_OS . ")\n";
            $message .= "Cannot continue. Aborting...\n";
            $level = LogLevel::CRITICAL;
            
            // Set error on fatal
            $this->error = true;
        
            break;

        case E_USER_WARNING:
            $message  = "Duality Warning [$errno] $errstr"
                . " on line $errline in file $errfile\n";
            $level = Psr\Log\LogLevel::ERROR;
            break;

        default:
            $message  = "Duality Notice [$errno] $errstr";
            $message .= !empty($errline) ? " on line $errline" : '';
            $message .= !empty($errfile) ? " in file $errfile" : '';
            $message .= "\n";
            break;
        }
        
        // Add date to message
        $message = date('Y-m-d H:i:s').": ".$message;
        
        // Log message
        $this->log($level, $message);

        /* Don't execute PHP internal error handler */
        return true;
    }
    
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        // Set error on fatal
        $this->error = true;
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function alert($message, array $context = array())
    {
        // Set error on fatal
        $this->error = true;
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function critical($message, array $context = array())
    {
        // Set error on fatal
        $this->error = true;
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * 
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $message = $level.': '.str_replace(array_keys($context), array_values($context), $message);
        $this->stream->write($message);
    }

}