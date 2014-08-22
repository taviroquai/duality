<?php

namespace Duality\System\Http;

use \Duality\System\Structure\Http;

/**
 * HTTP request class
 */
class Request extends Http {

	/**
     * Creates a new HTTP request
     */
	public function __construct($url = '')
	{
		$this->setUrl($url);
		$this->headers = array();
		$this->cookies = array();
		$this->isAjax = false;
	}
}