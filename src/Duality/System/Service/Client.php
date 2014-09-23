<?php

namespace Duality\System\Http;

use Duality\System\Core\InterfaceService;
use Duality\System\Structure\Url;
use Duality\System\App;

/**
 * Simulates an HTTP client
 */
class Client
{
	/**
     * Holds application container
     * @var Duality\System\App
     */
    protected $app;

    /**
     * Holds the client user agent
     * @var string
     */
	protected $useragent = 'Mozilla/5.0 (Windows NT 6.1; WOW64)';

    /**
     * Creates a new HTTP client
     * @param string $useragent
     */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
     * Creates a new HTTP client
     */
	public function init()
	{
		
	}

	/**
     * Terminates service
     */
    public function terminate()
    {

    }

    /**
     * Sets the user agent
     * @param string $useragent
     */
	public function setUserAgent($useragent)
	{
		$this->useragent = $useragent;	
	}

    /**
     * Gets the useragent
     * @return string
     */
	public function getUserAgent()
	{
		return $this->useragent;
	}

    /**
     * Creates a client request
     * @param string $url
     * @return \Duality\System\Http\Request
     */
	public static function createRequest(Url $url = '')
	{
		$request = new Request($url);
		return $request;
	}

    /**
     * Executes a request
     * @param \Duality\System\Http\Request $request
     * @return \Duality\System\Http\Response
     */
	public function execute(Request $request)
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

		$response = new Response;
		$response->setContent(curl_exec($ch));
		curl_close($ch);
		return $response;
	}
}