<?php

namespace Duality\System;

use Duality\System\Structure\Http;

class Server
{
	protected $hostname;

	protected $baseURL;

	protected $routes;

	public function __construct($hostname = 'localhost', $baseURL = '/')
	{
		$this->hostname = $hostname;
		$this->baseURL = $baseURL;
	}

	public function addRoute($uriPattern, $cb)
	{
		$this->routes[$uriPattern] = $cb;
	}

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

	public function setHostname($hostname)
	{
		$this->hostname = $hostname;
	}

	public function getHostname()
	{
		return $this->hostname;
	}

	public function createUrl($uri, $protocol = 'http')
	{
		return $protocol.'://'.$this->getHostname().$uri;
	}

	public function createResponse()
	{
		$response = new Http;
		$response->parseFromGlobals();
		return $response;
	}

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