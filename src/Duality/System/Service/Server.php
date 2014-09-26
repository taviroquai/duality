<?php

/**
 * HTTP server
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Structure\Url;
use Duality\System\Http\Request;
use Duality\System\Http\Response;
use Duality\System\App;

/**
 * Simulates an HTTP server
 */
class Server
implements InterfaceService
{
    /**
     * Holds application container
     * @var Duality\System\App
     */
    protected $app;

    /**
     * Default request
     * @var string
     */
    protected $request;

    /**
     * Default response
     * @var string
     */
    protected $response;

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
     * @param App $app
     */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

    /**
     * Initates service
     */
    public function init()
    {
        $this->hostname = $this->app->getConfigItem('server.hostname') ? 
            gethostname() : 
            $this->app->getConfigItem('server.hostname');
        $url = $this->app->getConfigItem('server.url') ? 
            $this->app->getConfigItem('server.url') : 
            '/';
        $this->baseURL = new Url($url);

        // Create default request and response
        $this->setRequest($this->getRequestFromGlobals());
        $this->setResponse($this->createResponse());
    }

    /**
     * Terminates service
     */
    public function terminate()
    {

    }

    /**
     * Adds a service route to the server
     * @param string $uriPattern
     * @param \Closure $cb
     */
	public function addRoute($uriPattern, $cb)
	{
		$this->routes[$uriPattern] = $cb;
	}

    /**
     * Adds a default service route to the server
     * @param \Closure $cb
     */
    public function addDefaultRoute($cb)
    {
        $this->addRoute('/^\/$/i', $cb);
    }

    /**
     * Starts server and run routes callbacks
     */
	public function listen()
	{
		foreach ($this->routes as $ns => $cb) {
            $uri = str_replace((string) $this->baseURL, '', $this->request->getUrl()->getUri());
			if ($result = preg_match($ns, $uri, $matches)) {
				$cb($this->request, $this->response, $matches);
			}
		}
        echo $this->send($this->response);
	}

    /**
     * Sets the request
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets the request
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the response
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Gets the response
     * @return Request
     */
    public function getResponse()
    {
        return $this->response;
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
     * @param string $scheme
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
            'Http-Accept'           => $_SERVER['HTTP_ACCEPT'],
            'Http-Accept-Charset'   => !empty($_SERVER['HTTP_ACCEPT_CHARSET']) ? 
                $_SERVER['HTTP_ACCEPT_CHARSET'] : !empty($_SERVER['HTTP_ACCEPT_ENCODING']) ? 
                $_SERVER['HTTP_ACCEPT_ENCODING'] : '',
            'Http-Host'             => empty($_SERVER['REMOTE_HOST']) ? 
                $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_HOST'],
            'Referer'               => empty($_SERVER['REFERER']) ? '' : $_SERVER['REFERER']
        );
        $request->setHeaders($headers);
        $request->setParams($_REQUEST);

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $request->setAjax(true);
        }
        return $request;
    }
}