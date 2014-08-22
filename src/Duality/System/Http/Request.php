<?php

namespace Duality\System\Http;

use \Duality\System\Structure\Http;
use \Duality\System\Structure\Url;

/**
 * HTTP request class
 */
class Request extends Http {

	/**
     * Creates a new HTTP request
     */
	public function __construct(Url $url = NULL)
	{
		$this->setUrl($url);
		$this->headers = array();
		$this->cookies = array();
		$this->isAjax = false;
	}
}