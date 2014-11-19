<?php

/**
 * Interface for HTTP server
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */

namespace Duality\Core;

use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;

/**
 * HTTP Server interface
 * 
 * Provides an interface for all Duality http servers
 * ie. \Duality\Service\Server
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */
interface InterfaceServer
{
    /**
     * Adds a service route to the server
     * 
     * @param string   $uriPattern Give the URI pattern as route identifier
     * @param \Closure $cb         The route callback
     * 
     * @return void
     */
    public function addRoute($uriPattern, $cb);

    /**
     * Starts server and run routes callbacks
     * 
     * @return void
     */
    public function listen();

    /**
     * Sets the request
     * 
     * @param \Duality\Structure\Http\Request $request Give the current request
     * 
     * @return void
     */
    public function setRequest(Request $request);

    /**
     * Gets the request
     * 
     * @return \Duality\Structure\Http\Request The current request
     */
    public function getRequest();

    /**
     * Sets the response
     * 
     * @param \Duality\Structure\Http\Response $response Give the current response
     * 
     * @return void
     */
    public function setResponse(Response $response);

    /**
     * Gets the response
     * 
     * @return \Duality\Structure\Http\Response The current response
     */
    public function getResponse();

    /**
     * Sets the server host name, used to parse request route
     * 
     * @param string $hostname Give the server a name
     * 
     * @return void
     */
    public function setHostname($hostname);

    /**
     * Gets the server host name, used to parse request route
     * 
     * @return string The server hostname
     */
    public function getHostname();

    /**
     * Writes HTTP response to application buffer
     * 
     * @param \Duality\Structure\Http $response    Give the server response
     * @param boolean                 $withHeaders Send headers or not
     * 
     * @return void
     */
    public function send(Response $response, $withHeaders = true);

    /**
     * Sends HTTP Headers if supported by SAPI
     * 
     * @param \Duality\Structure\Http\Response $response The response to be sent
     * 
     * @return \Duality\Service\Server This instance
     */
    public function sendHeaders(Response $response);

    /**
     * Sets an HTTP cookie
     * 
     * @param \Duality\Structure\Http\Response $response The response to be sent
     * 
     * @return \Duality\Service\Server This instance
     */
    public function sendCookies(Response $response);

}