<?php

namespace Duality\System\Service;

use Duality\System\Core\DualityException;
use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceErrorHandler;
use Duality\System\File\StreamFile;
use Duality\System\App;

/**
 * Default logger service
 */
class Logger implements InterfaceErrorHandler, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
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
	 * @param Duality\System\App $app
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
		$config = $this->app->getConfig();
		if (!isset($config['log_file'])) {
			throw new DualityException("Error Config: log_file configuration not found", 1);
		}
		$this->logFilePath = $this->app->getPath().DIRECTORY_SEPARATOR.$config['log_file'];
		if (!file_exists($this->logFilePath) || !is_writable($this->logFilePath)) {
			throw new DualityException("Error Logging: could not write to: ".$this->logFilePath, 1);
		}
		$this->file = new StreamFile($this->logFilePath);
		$this->file->open('a+b');
		set_error_handler(array($this, 'error'));
	}

	/**
	 * Terminate service
	 */
	public function terminate()
	{
		if ($this->error) {
			echo 'Ops! Something went wrong...';
		}
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
	        $msg  = "<b>My FATAR error</b> [$errno] $errstr<br />\n";
	        $msg .= "  on line $errline in file $errfile, PHP ". PHP_VERSION . " (" . PHP_OS . ")<br />\n";
	        $msg .= "Aborting...<br />\n";
	        break;

	    case E_USER_WARNING:
	        $msg = "<b>My WARNING</b> [$errno] $errstr<br />\n";
	        break;

	    case E_USER_NOTICE:
	        $msg = "<b>My NOTICE</b> [$errno] $errstr<br />\n";
	        break;

	    default:
	        $msg = "Unknown error type: [$errno] $errstr<br />\n";
	        break;
	    }

	    // write log
	    $this->file->setContent($this->file->getContent() . $msg);
	    $this->file->save();

	    // Set error
	    $this->error = true;

	    /* Don't execute PHP internal error handler */
	    return true;
	}

}