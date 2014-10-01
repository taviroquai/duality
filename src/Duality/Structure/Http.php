<?php

/**
 * Abstract HTTP transport structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Structure;

use \Duality\Core\DualityException;
use \Duality\Core\Structure;

/**
 * HTTP transport class
 */
abstract class Http 
extends Structure
{
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
     * Sets the HTTP url
     * @param \Duality\Structure\Url $url
     * @throws DualityException
     * @return Http;
     */
	public function setUrl(Url $url)
	{
		if (!filter_var((string) $url, FILTER_VALIDATE_URL)) {
			throw new DualityException("Invalid url", 11);
		}
		$this->url = $url;
        return $this;
	}

    /**
     * Gets the HTTP url 
     * @return \Duality\Structure\Url
     */
	public function getUrl()
	{
		return $this->url;
	}

    /**
     * Sets the HTTP method
     * @param string $method
     * @throws DualityException
     * @return Http;
     */
	public function setMethod($method)
	{
		if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))) {
			throw new DualityException("Invalid HTTP method", 7);
		}
		$this->method = $method;
        return $this;
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
     * @return Http;
     */
	public function setStatus($code)
	{
		$this->status = $code;
        return $this;
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
     * @throws DualityException
     * @return Http;
     */
	public function setHeaders($headers)
	{
		if (!is_array($headers)) {
			throw new DualityException("Headers must be an associative array", 8);
		}
		$this->headers = $headers;
        return $this;
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
     * @return Http;
     */
	public function addheader($key, $value)
	{
		$this->headers[$key] = $value;
        return $this;
	}

    /**
     * Sets an HTTP cookie
     * @param array $cookies
     * @throws DualityException
     * @return Http;
     */
	public function setCookies($cookies)
	{
		if (!is_array($cookies)) {
			throw new Exception("Cookies must be an array", 9);
		}
		foreach ($cookies as $item) {
			if (!is_array($item)) {
				throw new DualityException("Cookie must be an associative array", 10);
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
        return $this;
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
     * @return Http;
     */
	public function setContent($content)
	{
		$this->content = (string) $content;
        return $this;
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
     * @throws DualityException
     * @return Http;
     */
	public function setTimestamp($timestamp)
	{
		if (!is_numeric($timestamp) || (int)$timestamp !== $timestamp) {
			throw new Exception("Invalid connection timestamp", 12);
		}
		$this->timestamp = $timestamp;
        return $this;
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
     * @return Http;
     */
    public function setAjax($trueOrFalse)
    {
        $this->isAjax = (boolean) $trueOrFalse;
        return $this;
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