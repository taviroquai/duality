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
class Request extends Http
{
    /**
     * Holds the request params
     * 
     * @var \Duality\Core\InterfaceStorage Holds the request params as array
     */
    protected $params;

    /**
     * Creates a new HTTP request
     * 
     * @param \Duality\Structure\Url $url The request URL
     */
    public function __construct(Url $url = null)
    {
        parent::__construct();
        $this->params = new Storage;
        if (!empty($url)) {
            $this->setUrl($url);
        }
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
        $this->params->importArray($params);
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