<?php

namespace Duality\System;

use Duality\System\Structure\Http;
use Duality\System\Structure\Url;
use Duality\System\Http\Request;
use Duality\System\Http\Response;

/**
 * Simulates an HTTP server
 */
class Server
{
    /**
     * Server host name
     * @var string
     */
	protected $hostname;

    /**
     * Server base URL
     * @var string
     */
	protected $baseURL;

    /**
     * Server services routes
     * @var array
     */
	protected $routes;

    /**
     * Creates a new server
     * @param string $hostname
     * @param string $baseURL
     */
	public function __construct($hostname, Url $baseURL = NULL)
	{
		$this->hostname = $hostname;
        if (empty($baseURL)) {
            $baseURL = new Url('/');
        }
		$this->baseURL = $baseURL;
	}

    /**
     * Adds a service route to the server
     * @param string $uriPattern
     * @param \Clousure $cb
     */
	public function addRoute($uriPattern, $cb)
	{
		$this->routes[$uriPattern] = $cb;
	}

    /**
     * Adds a default service route to the server
     * @param \Clousure $cb
     */
    public function addDefaultRoute($cb)
    {
        $this->addRoute('/^\/$/i', $cb);
    }

    /**
     * Starts server and run routes callbacks
     * @param \Duality\System\Structure\Http $request
     * @param \Duality\System\Structure\Http $response
     */
	public function listen(Request $request, Response $response)
	{
		foreach ($this->routes as $ns => $cb) {
            $uri = str_replace((string) $this->baseURL, '', $request->getUrl()->getUri());
			if ($result = preg_match($ns, $uri, $matches)) {
				$cb($request, $response, $matches);
			}
		}
        echo $this->send($response);
	}

    /**
     * Sets the server host name
     * @param string $hostname
     */
	public function setHostname($hostname)
	{
		$this->hostname = $hostname;
	}

    /**
     * Gets the server host name
     * @return string
     */
	public function getHostname()
	{
		return $this->hostname;
	}

    /**
     * Creates a service URL
     * @param string $uri
     * @param string $protocol
     * @return string
     */
	public function createUrl($uri, $scheme = 'http')
	{
		return $scheme.'://'.$this->getHostname().$uri;
	}

    /**
     * Creates an HTTP response
     * @return \Duality\System\Structure\Http
     */
	public function createResponse()
	{
		$response = new Response;
		return $response;
	}

    /**
     * Echos HTTP response
     * @param \Duality\System\Structure\Http $response
     * @param boolean $withHeaders
     */
	public function send(Response $response, $withHeaders = true)
	{
		if ($withHeaders) {
			foreach ($response->getHeaders() as $k => $v) {
				header($k.': '.$v);
			}
		}
		echo $response->getContent();
	}

    /**
     * Parses HTTP properties from PHP global environment
     * @return Request
     */
    public static function getRequestFromGlobals()
    {
        $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . 
            "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $request = new Request(new Url($url));
        $request->setMethod($_SERVER['REQUEST_METHOD']);
        $request->setContent(file_get_contents('php://input'));
        $request->setTimestamp($_SERVER['REQUEST_TIME']);
        $headers = array(
            'Http-Accept' => $_SERVER['HTTP_ACCEPT'],
            'Http-Accept-Charset' => empty($_SERVER['HTTP_ACCEPT_CHARSET']) ? 
                $_SERVER['HTTP_ACCEPT_ENCODING'] : $_SERVER['HTTP_ACCEPT_CHARSET'],
            'Http-Host' => empty($_SERVER['REMOTE_HOST']) ? 
                $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_HOST'],
            'Referer' => empty($_SERVER['REFERER']) ? '' : $_SERVER['REFERER']
        );
        $request->setHeaders($headers);
        
        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $request->setAjax(true);
        }
        return $request;
    }
}