<?php

/**
 * HTTP server
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service\HTTPServer;

use Duality\Structure\Http\Response;
use Duality\Service\HTTPServer;

/**
 * HTTP server service
 * 
 * Provides operations for dealing with server requests/responses
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Native
extends HTTPServer
{
    /**
     * Sends HTTP Headers if supported by SAPI
     * 
     * @param \Duality\Structure\Http\Response $response The response to be sent
     * 
     * @return \Duality\Service\Server This instance
     */
    public function sendHeaders(Response $response)
    {   
        header(':', true, $response->getStatus());
        foreach ($response->getHeaders() as $k => $v) {
            header($k . ': ' . $v);
        }
        return $this;
    }

    /**
     * Sends HTTP cookies
     * 
     * @param Response $response The HTTP response
     * 
     * @return \Duality\Service\HTTPServer
     */
    public function sendCookies(Response $response)
    {
        $cookies = $response->getCookies();
        foreach ($cookies->asArray() as $name => $item) {
            setcookie(
                $name,
                $item['value'],
                $item['expire'],
                $item['path'],
                $item['domain'],
                $item['secure'],
                $item['httponly']
            );
        }
        return $this;
    }
}