<?php

/**
 * Interface for error handler
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

namespace Duality\Core;

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
	 * @param string $msg Default notice message
	 */
	public function log($msg);

	/**
	 * Terminates error handler
	 */
	public function terminate();
}