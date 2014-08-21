<?php

namespace Duality\System;

use Duality\System\Structure\Http;

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
	public function __construct($hostname = 'localhost', $baseURL = '/')
	{
		$this->hostname = $hostname;
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
     * Starts server and run routes callbacks
     * @param \Duality\System\Structure\Http $request
     * @param \Duality\System\Structure\Http $response
     */
	public function listen(Http $request, Http &$response)
	{
		foreach ($this->routes as $ns => $cb) {
			if (preg_match($ns, $request->getUrl())) {
				$cb($request, $response);
				$this->send($response);
				die();
			}
		}
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
	public function createUrl($uri, $protocol = 'http')
	{
		return $protocol.'://'.$this->getHostname().$uri;
	}

    /**
     * Creates an HTTP response
     * @return \Duality\System\Structure\Http
     */
	public function createResponse()
	{
		$response = new Http;
		$response->parseFromGlobals();
		return $response;
	}

    /**
     * Echos HTTP response
     * @param \Duality\System\Structure\Http $response
     * @param type $withHeaders
     */
	public function send(Http $response, $withHeaders = true)
	{
		if ($withHeaders) {
			foreach ($response->getHeaders() as $k => $v) {
				header($k.': '.$v);
			}
		}
		echo $response->getContent();
	}
}