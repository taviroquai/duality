<?php

/**
 * HTTP URL structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\Structure;

/**
 * Url class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Url 
extends Structure
{
    /**
     * Url scheme or protocol
     *
     * @param string The Url scheme
     */
    protected $scheme;

    /**
     * Hostname or domain name or machine name
     * 
     * @param string The url host
     */
    protected $host;

    /**
     * Url port
     * 
     * @param int The network port
     */
    protected $port;

    /**
     * User name
     * 
     * @param string The username
     */
    protected $user;

    /**
     * Password (very unsecure, avoid at all costs)
     * 
     * @param string The password
     */
    protected $pass;

    /**
     * Path or URI
     * 
     * @param string The URI
     */
    protected $path;

    /**
     * Query string
     * 
     * @param string The query string
     */
    protected $query;

    /**
     * URL fragment
     * 
     * @param string The fragment #
     */
    protected $fragment;

    /**
     * Creates a new url
     * 
     * @param string $url Give a valid URL
     */
    public function __construct($url = '')
    {
        $this->setName('url');
        if (!empty($url)) {
            $parts = parse_url($url);
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
     * 
     * @param string $host Give the URL host
     * 
     * @return void
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Gets the requested uri path
     * 
     * @return string The URI part
     */
    public function getUri()
    {
        return $this->path;
    }

    /**
     * Gets the full URL string
     * 
     * @return string The URL as a string
     */
    public function __toString()
    {
        $url = '';
        if (!empty($this->host)) {
            $url = empty($this->scheme) ? '' : $this->scheme.'://';
            $url .= empty($this->user) ? '' : $this->user;
            $url .= empty($this->pass) ? '' : ':'.$this->pass;
            $url .= empty($this->host) ? '' : 
                !empty($this->user) ? '@'.$this->host : $this->host;
            $url .= empty($this->port) ? '' : ':'.$this->port;
        }
        $url .= empty($this->path) ? '/' : $this->path;
        $url .= empty($this->query) ? '' : '?'.$this->query;
        $url .= empty($this->fragment) ? '' : '#'.$this->fragment;
        return $url;
    }
}