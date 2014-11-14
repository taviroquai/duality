<?php

/**
 * Abstract HTTP transport structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\DualityException;
use Duality\Core\Structure;

/**
 * HTTP transport class
 * 
 * PHP Version 5.3.4
 * 
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Http 
extends Structure
{
    /**
     * Holds the HTTP codes
     * 
     * @var array The HTTP codes list
     */
    protected $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );

    /**
     * HTTP url
     * 
     * @var string Holds the URL string
     */
    protected $url;
    
    /**
     * Holds the HTTP method
     * 
     * @var string Holds the HTTP method
     */
    protected $method;

    /**
     * Holds the HTTP transport status
     * 
     * @var int Holds the HTTP status code
     */
    protected $status;

    /**
     * Holds the HTTP headers associative array
     * 
     * @var \Duality\Core\InterfaceStorage Holds the HTTP headers
     */
    protected $headers;

    /**
     * Holds the HTTP transport cookies
     * 
     * @var \Duality\Core\InterfaceStorage Holds the user cookies
     */
    protected $cookies;

    /**
     * Holds the HTTP transport content
     * 
     * @var string Holds the HTTP content
     */
    protected $content;

    /**
     * Holds the HTTP transport timestamp
     * 
     * @var int Holds the connection timestamp
     */
    protected $timestamp;

    /**
     * Holds whether is an AJAX transport or not
     * 
     * @var boolean Holds the HTTPXMLRequest flag
     */
    protected $isAjax;

    /**
     * Creates a new HTTP instance
     */
    public function __construct()
    {
        $this->headers = new Storage;
        $this->cookies = new Storage;
        $this->isAjax = false;
    }

    /**
     * Sets the HTTP url
     * 
     * @param \Duality\Structure\Url $url Give the HTTP URL
     * 
     * @return Http This instance
     */
    public function setUrl(Url $url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Gets the HTTP url
     * 
     * @return \Duality\Structure\Url The HTTP URL
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the HTTP method
     * 
     * @param string $method Give the HTTP method
     * 
     * @throws DualityException When method is invalid
     * 
     * @return Http This instance
     */
    public function setMethod($method)
    {
        if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'))) {
            throw new DualityException(
                "Invalid HTTP method",
                DualityException::E_HTTP_METHODNOTFOUND
            );
        }
        $this->method = $method;
        return $this;
    }

    /**
     * Gets the HTTP method
     * 
     * @return string The HTTP method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets the HTTP connection status code
     * 
     * @param int $code Give the HTTP status code
     * 
     * @return Http This instance
     */
    public function setStatus($code)
    {
        if (in_array($code, array_keys($this->codes))) {
            $this->status = $code;    
        }
        return $this;
    }

    /**
     * Gets the HTTP code string
     * 
     * @return string This code string
     */
    public function getCodeString()
    {
        $key = $this->getStatus();
        return isset($this->codes[$key]) ? $this->codes[$key] : false;
    }

    /**
     * Gets the HTTP connection status code
     * 
     * @return int Returns the status code
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the HTTP headers
     * 
     * @param array $headers Sets the HTTP headers
     * 
     * @return Http This instance
     */
    public function setHeaders($headers)
    {
        $this->headers->importArray($headers);
        return $this;
    }

    /**
     * Gets all the HTTP headers
     * 
     * @return array The HTTP as associative array
     */
    public function getHeaders()
    {
        return $this->headers->asArray();
    }

    /**
     * Adds an HTTP header
     * 
     * @param string $key   The HTTP header key
     * @param string $value The HTTP header value
     * 
     * @return Http This instance
     */
    public function addheader($key, $value)
    {
        $this->headers->add($key, $value);
        return $this;
    }

    /**
     * Sets an HTTP cookie
     * 
     * @param array $cookies Give the cookie params
     * 
     * @return Http This instance
     */
    public function setCookies($cookies)
    {
        $cookies = (array) $cookies;
        foreach ($cookies as $k => &$item) {
            $item = (array) $item;
        }
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * Gets all HTTP cookies
     * 
     * @return array This instance
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Sets the HTTP content
     * 
     * @param string $content Give the HTTP content transport
     * 
     * @return Http;
     */
    public function setContent($content)
    {
        $this->content = (string) $content;
        return $this;
    }

    /**
     * Gets the HTTP content
     * 
     * @return string Returns the HTTP content
     */
    public function getContent()
    {
        return (string) $this->content;
    }

    /**
     * Sets the HTTP connection timestamp
     * 
     * @param int $timestamp Give the HTTP timestamp
     * 
     * @return Http This instance
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = (int) $timestamp;
        return $this;
    }

    /**
     * Gets the timestamp
     * 
     * @return int Returns the HTTP timestamp
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets whether the HTTP transport is ajax or not
     * 
     * @param boolean $trueOrFalse Gives the ajax flag ture or false
     * 
     * @return Http This instance
     */
    public function setAjax($trueOrFalse)
    {
        $this->isAjax = (boolean) $trueOrFalse;
        return $this;
    }

    /**
     * Gets whether is an AJAX transport or not
     * 
     * @return boolean Returns tru if is ajax or false otherwise
     */
    public function isAjax()
    {
        return (boolean) $this->isAjax;
    }
}