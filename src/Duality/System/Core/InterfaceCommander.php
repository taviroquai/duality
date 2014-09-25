<?php

/**
 * Interface for command line operations
 *
 * @since 		0.7.0
 * @author 		Marco Afonso <mafonso333@gmail.com>
 * @license 	MIT
 */

namespace Duality\System\Core;

/**
 * Commander interface
 */
interface InterfaceCommander
{
	/**
	 * Executes commander responders
	 */
	public function listen();

	/**
	 * Parses the command input
	 */
	public static function parseFromGlobals();

	/**
     * Adds command responder
     * @param string $uriPattern
     * @param \Closure $cb
     */
	public function addResponder($uriPattern, $cb);
}