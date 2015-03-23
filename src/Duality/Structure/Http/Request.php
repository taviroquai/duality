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
     * Creates a new HTTP request
     * 
     * @param \Duality\Structure\Url $url The request URL
     */
    public function __construct(Url $url = null)
    {
        parent::__construct();
        $this->params = new Storage;
        $this->routeParams = new Storage;
        if (!empty($url)) {
            $this->setUrl($url);
        }
    }
    
    /**
     * Validates authorization
     * 
     * @return boolean The authorization result
     */
    public function isAuthorized()
    {
        return true;
    }
    
    /**
     * Creates unauthorized response
     * 
     * @return \Duality\Structure\HTTP\Response
     */
    public function onUnauthorized()
    {
        return new Response();
    }
    
    /**
     * Imports from another request
     * 
     * @param \Duality\Structure\Http\Request $req
     */
    public function import(Request $req)
    {
        $this->setParams($req->getParams());
        $this->setRouteParams($req->getRouteParams());
        $this->setMethod($req->getMethod());
        $this->setHeaders($req->getHeaders());
        $this->setContent($req->getContent());
        $this->setStatus($req->getStatus());
        $this->setAjax($req->isAjax());
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
}