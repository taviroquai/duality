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
     * Sets the route params
     * 
     * @param array $params The route parameters
     * 
     * @return void
     */
    public function setRouteParams($params)
    {
        $this->routeParams->importArray((array)$params);
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
     * Sets the request params
     * 
     * @param array $params Give the request parameters
     * 
     * @return void
     */
    public function setParams($params)
    {
        $this->params->importArray((array)$params);
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
     * Get one request param by key
     * 
     * @param string $key Give the parameter key to identify the value
     * 
     * @return string|null The resulting item or null
     */
    public function getParam($key)
    {
        return $this->hasParam($key) ? $this->params->get($key) : null;
    }

    /**
     * Checks whether has a param or not
     * 
     * @param string $key Give the key to identify the item
     * 
     * @return boolean Tells whether the key exists or not
     */
    public function hasParam($key)
    {
        return $this->params->has($key);
    }

    /**
     * Get one request header by key
     * 
     * @param string $key Give the header key
     * 
     * @return string|null
     */
    public function getHeaderItem($key)
    {
        return $this->hasHeader($key) ? $this->headers->get($key) : null;
    }

    /**
     * Checks whether has a header item or not
     * 
     * @param string $key Give the key to tell if header exists or not
     * 
     * @return boolean The check result
     */
    public function hasHeader($key)
    {
        return $this->headers->has($key);
    }
    
    /**
     * Sets the native server params
     * 
     * @param array $server The native server params
     */
    public function setNativeServer($server)
    {
        $this->nativeServer = $server;
    }
    
    /**
     * Sets the native request params
     * 
     * @param array $request The native request params
     */
    public function setNativeRequest($request)
    {
        $this->nativeRequest = $request;
    }
    
    /**
     * Parses HTTP properties
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
        $this->setParams($this->nativeRequest);

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
}