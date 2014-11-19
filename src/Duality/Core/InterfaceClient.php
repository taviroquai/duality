<?php

/**
 * Interface for HTTP client
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */

namespace Duality\Core;

use Duality\Core\InterfaceUrl;
use Duality\Structure\Http\Request;

/**
 * HTTP Client interface
 * 
 * Provides an interface for all Duality http clients
 * ie. \Duality\Service\Client
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */
interface InterfaceClient
{
    /**
     * Sets the user agent
     * 
     * @param string $useragent Give the HTTP user-agent string
     * 
     * @return void
     */
    public function setUserAgent($useragent);

    /**
     * Gets the useragent
     * 
     * @return string The HTTP user-agent string
     */
    public function getUserAgent();

    /**
     * Creates a client request
     * 
     * @param \Duality\Core\InterfaceUrl $url Give the URL to create the request
     * 
     * @return \Duality\Structure\Http\Request The HTTP request
     */
    public static function createRequest(InterfaceUrl $url = null);

    /**
     * Executes a request
     * 
     * @param \Duality\Structure\Http\Request &$request Give the HTTP request
     * 
     * @return \Duality\Structure\Http\Response The resulting HTTP response
     */
    public function execute(Request &$request);

}