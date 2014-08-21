<?php

namespace Duality\System;

use Duality\System\Structure\Http;

class Client
{

	protected $useragent;

	public function __construct($useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64)')
	{
		$this->useragent = $useragent;
	}

	public function setUserAgent($useragent)
	{
		$this->useragent = $useragent;	
	}

	public function getUserAgent()
	{
		return $this->useragent;
	}

	public function createRequest($url)
	{
		$request = new Http;
		$request->setUrl($url);
		return $request;
	}

	public function execute(Http $request)
	{
		$ch = curl_init($request->getUrl());

		$header = array();
		$header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"; 
		$header[] = "Cache-Control: max-age=0"; 
		$header[] = "Connection: keep-alive"; 
		$header[] = "Keep-Alive:timeout=5, max=100"; 
		$header[] = "Accept-Charset:utf-8,ISO-8859-1;q=0.7,*;q=0.3"; 
		$header[] = "Accept-Language:en-US,en;q=0.8"; 
		$header[] = "Pragma: ";

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_REFERER, 'http://localhost');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,sdch'); 
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);

		$response = new Http;
		$response->setContent(curl_exec($ch));
		curl_close($ch);
		return $response;
	}
}