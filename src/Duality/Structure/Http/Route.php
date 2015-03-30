<?php

/**
 * HTTP route structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Structure\Http;

use Duality\Core\AbstractStructure;

/**
 * Property class
 * 
 * Provides extended functionality for entities and table properties
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
class Route 
extends AbstractStructure
{
    /**
     * Holds the HTTP request
     * 
     * @var string
     */
    protected $request;
    
    /**
     * Holds the HTTP response
     * 
     * @var string
     */
    protected $response;

    /**
     * Creates a new property
     * 
     * @param string $name Give a name to property
     */
    public function __construct($name = '')
    {
        if (!empty($name)) {
            $this->setName($name);
        }
    }
    
    /**
     * Sets the HTTP request
     * 
     * @param string $req The request
     * 
     * @return void
     */
    public function setRequest($req)
    {
        $this->request = (string) $req;
    }
    
    /**
     * Sets the HTTP response
     * 
     * @param string $res The response
     * 
     * @return void
     */
    public function setResponse($res)
    {
        $this->response = (string) $res;
    }
    
    /**
     * Gets the HTTP request
     * 
     * @return string The request
     */
    public function getRequest()
    {
        return (string) $this->request;
    }
    
    /**
     * Gets the HTTP response
     * 
     * @return string
     */
    public function getResponse()
    {
        return (string) $this->response;
    }
}