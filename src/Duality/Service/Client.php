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
     * Holds the current curl handler
     * 
     * @var resource The curl handler
     */
    protected $curl;

    /**
     * Holds the last response information
     * 
     * @var array The last response
     */
    protected $response;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (gettype($this->curl) == 'resource') {
            curl_close($this->curl);
        }
        $this->curl = curl_init();
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
     * Gets the current curl handler
     * 
     * @return resource The curl handler
     */
    public function getCurlHandler()
    {
        return $this->curl;
    }

    /**
     * Creates a client request
     * 
     * @param string $url Give the URL to create the request
     * 
     * @return \Duality\Structure\Http\Request The HTTP request
     */
    public static function createRequest(Url $url = null)
    {
        return new Request($url);
    }

    /**
     * Executes a request
     * 
     * @param \Duality\Structure\Http\Request &$request Give the HTTP request
     * 
     * @return \Duality\Structure\Http\Response The resulting HTTP response
     */
    public function execute(Request &$request)
    {
        $this->init();
        $header = array();
        $reqHeaders = $request->getHeaders(); 
        if (empty($reqHeaders)) {
            $header[] = "Accept: text/html,application/xhtml+xml,application/xml;"
                . "q=0.9,*/*;q=0.8"; 
            $header[] = "Cache-Control: max-age=0"; 
            $header[] = "Connection: keep-alive"; 
            $header[] = "Keep-Alive: timeout=5, max=100"; 
            $header[] = "Accept-Charset: utf-8,ISO-8859-1;q=0.7,*;q=0.3"; 
            $header[] = "Accept-Language: en-US,en;q=0.8"; 
            $header[] = "Pragma: ";
        } else {
            foreach ($request->getHeaders() as $key => $item) {
                $header[] = $key . ': ' . $item;
            }
        }

        curl_setopt($this->curl, CURLOPT_URL,               $request->getUrl());
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,    1);
        curl_setopt($this->curl, CURLOPT_USERAGENT,         $this->getUserAgent());
        curl_setopt($this->curl, CURLOPT_HTTPHEADER,        $header);
        curl_setopt($this->curl, CURLOPT_REFERER,           'http://localhost');
        curl_setopt($this->curl, CURLOPT_ENCODING,          'gzip,deflate,sdch'); 
        curl_setopt($this->curl, CURLOPT_AUTOREFERER,       true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT,           3);
        curl_setopt($this->curl, CURLOPT_HEADER,            1);

        
        $result = curl_exec($this->curl);
        $this->response = $this->parseResult($result);
        curl_close($this->curl);
        return $this->response;
    }

    /**
     * Parse the response string
     * 
     * @param string $result The response string
     * 
     * @return \Duality\Structure\Http\Response The response instance
     */
    protected function parseResult($result)
    {
        list($header, $body) = @explode("\r\n\r\n", $result, 2);
        $response = new Response;
        $response->setContent($body);
        foreach (explode("\r\n", $header) as $i => $line) {
            if ($i === 0) {
                $parts = explode(' ', $line);
                $response->setStatus($parts[1]);
                unset($parts);
            } else {
                list ($key, $value) = explode(': ', $line);
                $response->addHeader($key, $value);
            }
        }
        return $response;
    }
}