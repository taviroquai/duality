<?php

/**
 * HTTP client service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Structure\Url;
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;

/**
 * Simulates an HTTP client
 * 
 * PHP Version 5.3.3
 * 
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Client
extends AbstractService
{
    /**
     * Holds the client user agent
     * 
     * @var string The default HTTP user-agent header
     */
    protected $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64)';

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {

    }

    /**
     * Sets the user agent
     * 
     * @param string $useragent Give the HTTP user-agent string
     * 
     * @return void
     */
    public function setUserAgent($useragent)
    {
        $this->useragent = $useragent;  
    }

    /**
     * Gets the useragent
     * 
     * @return string The HTTP user-agent string
     */
    public function getUserAgent()
    {
        return $this->useragent;
    }

    /**
     * Creates a client request
     * 
     * @param string $url Give the URL to create the request
     * 
     * @return \Duality\Http\Request The HTTP request
     */
    public static function createRequest(Url $url = null)
    {
        $request = new Request($url);
        return $request;
    }

    /**
     * Executes a request
     * 
     * @param \Duality\Http\Request $request Give the HTTP request
     * 
     * @return \Duality\Http\Response The resulting HTTP response
     */
    public function execute(Request $request)
    {
        $ch = curl_init($request->getUrl());

        $header = array();
        $header[] = "Accept:text/html,application/xhtml+xml,application/xml;"
            . "q=0.9,*/*;q=0.8"; 
        $header[] = "Cache-Control: max-age=0"; 
        $header[] = "Connection: keep-alive"; 
        $header[] = "Keep-Alive:timeout=5, max=100"; 
        $header[] = "Accept-Charset:utf-8,ISO-8859-1;q=0.7,*;q=0.3"; 
        $header[] = "Accept-Language:en-US,en;q=0.8"; 
        $header[] = "Pragma: ";

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    1);
        curl_setopt($ch, CURLOPT_USERAGENT,         $this->getUserAgent());
        curl_setopt($ch, CURLOPT_HTTPHEADER,        $header);
        curl_setopt($ch, CURLOPT_REFERER,           'http://localhost');
        curl_setopt($ch, CURLOPT_ENCODING,          'gzip,deflate,sdch'); 
        curl_setopt($ch, CURLOPT_AUTOREFERER,       true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           3);

        $response = new Response;
        $response->setContent(curl_exec($ch));
        curl_close($ch);
        return $response;
    }
}