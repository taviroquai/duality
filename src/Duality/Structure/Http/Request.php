<?php

/**
 * HTTP request structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\Http;

use Duality\Core\InterfaceUrl;
use Duality\Core\InterfaceAuthorization;
use Duality\Structure\Storage;
use Duality\Structure\Http;
use Duality\Structure\Url;

/**
 * HTTP request class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Request
extends Http
implements InterfaceAuthorization
{
    /**
     * Holds the request params
     * 
     * @var \Duality\Core\InterfaceStorage Holds the request params as array
     */
    protected $params;
    
    /**
     * Holds the route params
     * 
     * @var \Duality\Core\InterfaceStorage Holds the request params as array
     */
    protected $routeParams;
    
    /**
     * Holds the native server
     * 
     * @var array The server params
     */
    protected $nativeServer = array();
    
    /**
     * Holds the native request
     * 
     * @var array The request params
     */
    protected $nativeRequest = array();

    /**
     * Creates a new HTTP request
     * 
     * @param \Duality\Core\InterfaceUrl $url The request URL
     */
    public function __construct(InterfaceUrl $url = null)
    {
        parent::__construct();
        $this->params = new Storage;
        $this->routeParams = new Storage;
        if (!empty($url)) {
            $this->setUrl($url);
        }
        
        // Load native data
        $this->nativeServer = $_SERVER;
        $this->nativeRequest = $_REQUEST;
    }
    
    /**
     * Validates authorization
     * 
     * @return boolean The authorization result
     */
    public function isAuthorized(Response &$response)
    {
        $response->onRequest($this);
    }

    /**
     * Gets the route params as array
     * 
     * @return array The parameters
     */
    public function getRouteParams()
    {
        return $this->routeParams->asArray();
    }
    
    /**
     * Get one route param by key
     * 
     * @param string $key Give the parameter key to identify the value
     * 
     * @return string|null The resulting item or null
     */
    public function getRouteParam($key)
    {
        return $this->routeParams->has($key) ? $this->routeParams->get($key) : null;
    }

    /**
     * Gets the request params as array
     * 
     * @return array The parameters
     */
    public function getParams()
    {
        return $this->params->asArray();
    }

    /**
     * Get one request header by key
     * 
     * @param string $key Give the header key
     * 
     * @return string|null
     */
    public function getHeader($key)
    {
        return $this->headers->get((string) $key);
    }
    
    /**
     * Sets the request param value
     * 
     * @param string $name  The param name
     * @param mixed  $value The param value
     * 
     * @return void
     */
    public function __set($name, $value) {
        $this->params->set($name, $value);
    }
    
    /**
     * Gets the request param value
     * 
     * @param string $name The param name
     * 
     * @return mixed The param value
     */
    public function __get($name) {
        return $this->params->get((string)$name);
    }
    
    /**
     * Returns whether there is a param or not
     * 
     * @param string $name The param name
     * 
     * @return boolean
     */
    public function __isset($name) {
        return $this->params->has((string) $name);
    }
    
    /**
     * Unsets a param
     * 
     * @param string $name The param name
     * 
     * @return void
     */
    public function __unset($name) {
        $this->params->remove((string) $name);
    }
    
    /**
     * Whether matches the route or not
     * 
     * @param string $route The route expression
     * 
     * @return boolean
     */
    public function matchRoute($route)
    {
        $uri = $this->getUrl()->getUri();
        if (preg_match($route, $uri, $matches)) {
            foreach ((array) array_shift($matches) as $key => $value) {
                $this->routeParams->set($key, $value);
            }
            return true;
        }
        return false;
    }

    /**
     * Imports globals
     * 
     * @return \Duality\Structure\Http\Request Whether there is an HTTP request or not
     */
    public function importFromGlobals()
    {   
        // Filter input
        array_filter($this->nativeServer, function(&$var) {
            $var = filter_var($var, FILTER_UNSAFE_RAW);
        });
        array_filter($this->nativeRequest, function(&$var) {
            $var = filter_var($var, FILTER_UNSAFE_RAW);
        });
        
        // Detect base URL and URI
        $this->nativeServer['SCRIPT_NAME'] = empty($this->nativeServer['SCRIPT_NAME']) ? 
                '/index.php' : $this->nativeServer['SCRIPT_NAME'];
        $this->nativeServer['REQUEST_URI'] = empty($this->nativeServer['REQUEST_URI']) ?
                '/' : $this->nativeServer['REQUEST_URI'];
        $uri = $this->nativeServer['REQUEST_URI'];
        $uri = str_replace(dirname($this->nativeServer['SCRIPT_NAME']), '', $uri);
        $uri = str_replace(basename($this->nativeServer['SCRIPT_NAME']), '', $uri);
        $uri = '/' . trim($uri, '/');
        $baseUrl = (empty($this->nativeServer['HTTPS']) ? 'http' : 'https')
            . "://"
            . (empty($this->nativeServer['SERVER_NAME']) ? gethostname() : $this->nativeServer['SERVER_NAME']);
        ;
        
        // Set base URL and URI strings
        $this->setUrl(new Url($baseUrl . $uri));
        
        $this->setMethod(empty($this->nativeServer['REQUEST_METHOD']) ? 'GET' : $this->nativeServer['REQUEST_METHOD']);
        $this->setContent(file_get_contents('php://input'));
        $this->setTimestamp(
            empty($this->nativeServer['REQUEST_TIME']) ? time() : $this->nativeServer['REQUEST_TIME']
        );
        $headers = array(
            'Http-Accept'           => empty($this->nativeServer['HTTP_ACCEPT']) ? 
                'text/html' : $this->nativeServer['HTTP_ACCEPT'],
            'Http-Accept-Language'  => !empty($this->nativeServer['HTTP_ACCEPT_LANGUAGE']) ?
                $this->nativeServer['HTTP_ACCEPT_LANGUAGE'] : 'en-US',
            'Http-Accept-Charset'   => !empty($this->nativeServer['HTTP_ACCEPT_CHARSET']) ? 
                $this->nativeServer['HTTP_ACCEPT_CHARSET'] : 
                !empty($this->nativeServer['HTTP_ACCEPT_ENCODING']) ? 
                $this->nativeServer['HTTP_ACCEPT_ENCODING'] : 'utf-8',
            'Http-Host'             => empty($this->nativeServer['REMOTE_HOST']) ? 
                empty($this->nativeServer['REMOTE_ADDR']) ? '' : $this->nativeServer['REMOTE_ADDR']
                : $this->nativeServer['REMOTE_HOST'],
            'Referer'               => empty($this->nativeServer['REFERER']) ? 
                '' : $this->nativeServer['REFERER']
        );
        $this->setHeaders($headers);
        foreach ($this->nativeRequest as $key => $item) {
            $this->$key = $item;
        }

        if (!empty($this->nativeServer['HTTP_X_REQUESTED_WITH']) 
            && strtolower($this->nativeServer['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $this->setAjax(true);
        }
        return $this;
    }
    
    /**
     * Gets the base URL from globals
     * 
     * @return string
     */
    public function getBaseUrlFromGlobals()
    {
        return (empty($this->nativeServer['HTTPS']) ? 'http' : 'https')
            . "://"
            . (empty($this->nativeServer['SERVER_NAME']) ? gethostname() : $this->nativeServer['SERVER_NAME'])
            . dirname($this->nativeServer['SCRIPT_NAME']);
    }
    
    /**
     * Check if there is an HTTP request
     * 
     * @return boolean Whether there is an HTTP request or not
     */
    public function validateHTTP()
    {
        return !empty($this->nativeServer['REQUEST_METHOD']);
    }
    
    /**
     * Filter data
     * 
     * @param string &$data Give the data to be decrypted
     * @param int    $type  Give the type of filter
     * 
     * @return void
     */
    public function filter(&$data, $type = FILTER_UNSAFE_RAW)
    {
        if (is_array($data)) {
            foreach ($data as $key => &$item) {
                $this->filter($item, $type);
            }
        }
        filter_var($data, $type);
    }
}