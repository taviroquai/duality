<?php

namespace Duality\System\Structure;

use \Duality\System\Core\Structure;

/**
 * HTTP transport class
 */
abstract class Http extends Structure {

    /**
     * HTTP url
     * @var string 
     */
	protected $url;
	
    /**
     * Holds the HTTP method
     * @var string
     */
	protected $method;

    /**
     * Holds the HTTP transport status
     * @var int
     */
	protected $status;

    /**
     * Holds the HTTP headers associative array
     * @var array
     */
	protected $headers;

    /**
     * Holds the HTTP transport cookies
     * @var array
     */
	protected $cookies;

    /**
     * Holds the HTTP transport content
     * @var string
     */
	protected $content;

    /**
     * Holds the HTTP transport timestamp
     * @var int
     */
	protected $timestamp;

    /**
     * Holds whether is an AJAX transport or not
     * @var boolean
     */
	protected $isAjax;

    /**
     * Parses HTTP properties from PHP global environment
     */
	public function parseFromGlobals()
	{
		$this->setMethod($_SERVER['REQUEST_METHOD']);
		$this->setContent(file_get_contents('php://input'));
		$this->setTimestamp($_SERVER['REQUEST_TIME']);
		$headers = array(
			'Http-Accept' => $_SERVER['HTTP_ACCEPT'],
			'Http-Accept-Charset' => empty($_SERVER['HTTP_ACCEPT_CHARSET']) ? 
                $_SERVER['HTTP_ACCEPT_ENCODING'] : $_SERVER['HTTP_ACCEPT_CHARSET'],
			'Http-Host' => empty($_SERVER['REMOTE_HOST']) ? 
                $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_HOST'],
			'Referer' => empty($_SERVER['REFERER']) ? '' : $_SERVER['REFERER']
		);
		$this->setHeaders($headers);
        $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . 
            "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		$this->setUrl($url);
		if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
			$this->isAjax = true;
		}
	}

    /**
     * Sets the HTTP url
     * @param string $url
     * @throws \Exception
     */
	public function setUrl($url)
	{
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new \Exception("Invalid url", 11);
		}
		$this->url = $url;
	}

    /**
     * Gets the HTTP url 
     * @return string
     */
	public function getUrl()
	{
		return $this->url;
	}

    /**
     * Sets the HTTP method
     * @param string $method
     * @throws \Exception
     */
	public function setMethod($method)
	{
		if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))) {
			throw new Exception("Invalid HTTP method", 7);
		}
		$this->method = $method;
	}

    /**
     * Gets the HTTP method
     * @return string
     */
	public function getMethod()
	{
		return $this->method;
	}

    /**
     * Sets the HTTP connection status code
     * @param int $code
     */
	public function setStatus($code)
	{
		$this->status = $code;
	}

    /**
     * Gets the HTTP connection status code
     * @return int
     */
	public function getStatus()
	{
		return $this->status;
	}

    /**
     * Sets the HTTP headers
     * @param array $headers
     * @throws \Exception
     */
	public function setHeaders($headers)
	{
		if (!is_array($headers)) {
			throw new Exception("Headers must be an associative array", 8);
		}
		$this->headers = $headers;
	}

    /**
     * Gets all the HTTP headers
     * @return array
     */
	public function getHeaders()
	{
		return $this->headers;
	}

    /**
     * Adds an HTTP header
     * @param string $key
     * @param string $value
     */
	public function addheader($key, $value)
	{
		$this->headers[$key] = $value;
	}

    /**
     * Sets an HTTP cookie
     * @param array $cookies
     * @throws \Exception
     */
	public function setCookies($cookies)
	{
		if (!is_array($cookies)) {
			throw new Exception("Cookies must be an array", 9);
		}
		foreach ($cookies as $item) {
			if (!is_array($item)) {
				throw new Exception("Cookie must be an associative array", 10);
			}
			setcookie(
                $item['name'],
                $item['value'],
                $item['expire'],
                $item['path'],
                $item['domain'],
                $item['secure']
            );
		}
	}

    /**
     * Gets all HTTP cookies
     * @return array
     */
	public function getCookies()
	{
		return $this->cookies;
	}

    /**
     * Sets the HTTP content
     * @param string $content
     */
	public function setContent($content)
	{
		$this->content = (string) $content;
	}

    /**
     * Gets the HTTP content
     * @return string
     */
	public function getContent()
	{
		return $this->content;
	}

    /**
     * Sets the HTTP connection timestamp
     * @param int $timestamp
     * @throws \Exception
     */
	public function setTimestamp($timestamp)
	{
		if (!is_numeric($timestamp) || (int)$timestamp !== $timestamp) {
			throw new Exception("Invalid connection timestamp", 12);
		}
		$this->timestamp = $timestamp;
	}

    /**
     * Gets the timestamp
     * @return int
     */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

    /**
     * Sets whether the HTTP transport is ajax or not
     * @param boolean $trueOrFalse
     */
    public function setAjax($trueOrFalse)
    {
        $this->isAjax = (boolean) $trueOrFalse;
    }

    /**
     * Gets whether is an AJAX transport or not
     * @return boolean
     */
	public function isAjax()
	{
		return (boolean) $this->isAjax;
	}
}