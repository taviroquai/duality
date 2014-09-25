<?php

namespace Duality\System\Http;

use \Duality\System\Structure\Http;
use \Duality\System\Structure\Url;

/**
 * HTTP request class
 */
class Request extends Http {

	/**
	 * Holds the request params
	 * @var array
	 */
	protected $params = array();

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

	/**
	 * Sets the request params
	 * @param array $params
	 */
	public function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * Gets the request params
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Get one request param by key
	 * @return array
	 */
	public function getParam($key)
	{
		return $this->hasParam($key) ? $this->params[$key] : NULL;
	}

	/**
	 * Checks whether has a param or not
	 * @return boolean
	 */
	public function hasParam($key)
	{
		return isset($this->params[$key]);
	}
}