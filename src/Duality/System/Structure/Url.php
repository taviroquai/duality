<?php

namespace Duality\System\Structure;

use Duality\System\Core\DualityException;
use Duality\System\Core\Structure;

/**
 * Url class
 */
class Url extends Structure
{
	/**
	 * Url scheme or protocol
	 * @param string
	 */
	protected $scheme;

	/**
	 * Hostname or domain name or machine name
	 * @param string
	 */
	protected $host;

	/**
	 * Url port
	 * @param int
	 */
	protected $port;

	/**
	 * User name
	 * @param string
	 */
	protected $user;

	/**
	 * Password (very unsecure, avoid at all costs)
	 * @param string
	 */
	protected $pass;

	/**
	 * Path or URI
	 * @param string
	 */
	protected $path;

	/**
	 * Query string
	 * @param string
	 */
	protected $query;

	/**
	 * URL fragment
	 */
	protected $fragment;

    /**
     * Creates a new url
     * @param string $url
     */
	public function __construct($url = '')
	{
		$this->setName('url');
		if (!empty($url)) {
			$parts = @parse_url($url);
			if (empty($parts)) {
				throw new DualityException("Error parsing URL", 13);
			}
			$this->scheme = empty($parts['scheme']) ? '' : $parts['scheme'];
			$this->user = empty($parts['user']) ? '' : $parts['user'];
			$this->pass = empty($parts['pass']) ? '' : $parts['path'];
			$this->host = empty($parts['host']) ? '' : $parts['host'];
			$this->port = empty($parts['port']) ? '' : $parts['port'];
			$this->path = empty($parts['path']) ? '/' : $parts['path'];
			
			$this->query = empty($parts['query']) ? '' : $parts['query'];
			$this->fragment = empty($parts['fragment']) ? '' : $parts['fragment'];
		}
	}

	/**
	 * Sets the URL hostname
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}

	/**
	 * Gets the full URL string
	 */
	public function __toString()
	{
		$url = '';
		if (!empty($this->host)) {
			$url = empty($this->scheme) ? '' : $this->scheme.'://';
			$url .= empty($this->user) ? '' : $this->user;
			$url .= empty($this->pass) ? '' : ':'.$this->pass;
			$url .= empty($this->host) ? '' : !empty($this->user) ? '@'.$this->host : '';
			$url .= empty($this->port) ? '' : $this->port;
		}
		$url .= empty($host) ? ltrim($this->path, '/') : $this->path;
		$url .= empty($this->query) ? '' : '?'.$this->query;
		$url .= empty($this->fragment) ? '' : '#'.$this->fragment;
		return $url;
	}
}