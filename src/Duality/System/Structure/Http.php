<?php

namespace Duality\System\Structure;

use \Duality\System\Core\Structure;

class Http extends Structure {

	protected $url;
	
	protected $method;

	protected $status;

	protected $headers;

	protected $cookies;

	protected $content;

	protected $timestamp;

	protected $isAjax;
	
	public function __construct()
	{
		parent::__construct();
		$this->headers = array();
		$this->cookies = array();
		$this->isAjax = false;
	}

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

	public function setUrl($url)
	{
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			throw new Exception("Invalid url", 11);
		}
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setMethod($method)
	{
		if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))) {
			throw new Exception("Invalid HTTP method", 7);
		}
		$this->method = $method;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function setStatus($code)
	{
		$this->status = $code;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setHeaders($headers)
	{
		if (!is_array($headers)) {
			throw new Exception("Headers must be an associative array", 8);
		}
		$this->headers = $headers;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function addheader($key, $value)
	{
		$this->headers[$key] = $value;
	}

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

	public function getCookies()
	{
		return $this->cookies;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setTimestamp($timestamp)
	{
		if (!is_numeric($timestamp) || (int)$timestamp !== $timestamp) {
			throw new Exception("Invalid connection timestamp", 12);
		}
		$this->timestamp = $timestamp;
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}

	public function isAjax()
	{
		return (boolean) $this->isAjax;
	}
}