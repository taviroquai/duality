<?php

namespace Duality\System\Core;

/**
 * Default error handler
 */
interface InterfaceErrorHandler
{
	/**
	 * Default error action
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function error($errno, $errstr, $errfile, $errline);

	/**
	 * Log action
	 * @param string $errstr
	 */
	public function log($msg);

	/**
	 * Terminates error handler
	 */
	public function terminate();
}