<?php

/**
 * HTTP response structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Http;

use \Duality\System\Structure\Http;

/**
 * HTTP response class
 */
class Response extends Http {

	/**
     * Creates a new HTTP response
     */
	public function __construct()
	{
		$this->headers = array();
		$this->cookies = array();
		$this->isAjax = false;
	}
}