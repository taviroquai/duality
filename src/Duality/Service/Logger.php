<?php

/**
 * Logger service (file)
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\InterfaceService;
use Duality\Core\InterfaceErrorHandler;
use Duality\File\StreamFile;
use Duality\App;

/**
 * Default logger service
 */
class Logger 
implements InterfaceErrorHandler, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\App
	 */
	protected $app;
	
	/**
	 * Default log file path
	 * @var string
	 */
	protected $logFilePath = './data/logs.txt';

	/**
	 * Holds the file that will receive the log
	 */
	protected $file;

	/**
	 * Holds if there was any error or not
	 */
	protected $error = false;

	/**
	 * Creates a new error handler
	 * @param Duality\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		if (!$this->app->getConfigItem('logger.file')) {
			throw new DualityException("Error Config: log_file configuration not found", 1);
		}
		$this->logFilePath = $this->app->getPath()
			.DIRECTORY_SEPARATOR
			.$this->app->getConfigItem('logger.file');
		if (!file_exists($this->logFilePath) || !is_writable($this->logFilePath)) {
			throw new DualityException("Error Logging: could not write to: ".$this->logFilePath, 1);
		}
		$this->file = new StreamFile($this->logFilePath);
		$this->file->open('a+b');
		set_error_handler(array($this, 'error'));
		set_exception_handler(array($this, 'myException'));
	}

	/**
	 * Terminate service
	 */
	public function terminate()
	{
		if ($this->error) {
			echo 'Ops! Something went wrong...';
		}
		$this->file->close();
	}

	/**
	 * Log action
	 * @param string $msg
	 */
	public function log($msg)
	{
		trigger_error($msg);
	}

	/**
	 * Handles exceptions
	 * @param \Exception $e
	 */
	public function myException($e)
	{
		$this->error(E_USER_ERROR, $e->getMessage(), $e->getFile(), $e->getLine());
	}

	/**
	 * Default error handler
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function error($errno, $errstr, $errfile, $errline)
	{
	    if (!(error_reporting() & $errno)) {
	        // This error code is not included in error_reporting
	        return;
	    }

	    $msg = '';

	    switch ($errno) {
	    case E_USER_ERROR:
	        $msg  = "My Fatal Error [$errno] $errstr on line $errline in file $errfile\n";
	        $msg .= "PHP ". PHP_VERSION . " (" . PHP_OS . ")\n";
	        $msg .= "Cannot continue. Aborting...\n";
	        break;

	    case E_USER_WARNING:
	        $msg  = "My Warning [$errno] $errstr on line $errline in file $errfile\n";
	        break;

	    case E_USER_NOTICE:
	        $msg  = "My Notice [$errno] $errstr on line $errline in file $errfile\n";
	        break;

	    default:
	        $msg  = "My Notice [$errno] $errstr on line $errline in file $errfile\n";
	        break;
	    }
        
        // Add date to message
        $msg = date('Y-m-d H:i:s').": ".$msg;

	    // write log
	    $this->file->write($msg);

	    // Set error
	    $this->error = true;

	    /* Don't execute PHP internal error handler */
	    return true;
	}

}