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

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Core\InterfaceHTTPServer;
use Duality\Core\InterfaceUrl;
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;
use Duality\App;

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
abstract class HTTPServer
extends AbstractService
implements InterfaceHTTPServer
{
    /**
     * Holds the HTTP codes
     * 
     * @var array The HTTP codes list
     */
    static $httpCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );
    
    /**
     * Default request
     * 
     * @var \Duality\Structure\Http\Request Holds the current request
     */
    protected $request;

    /**
     * Default response
     * 
     * @var \Duality\Structure\Http\Response Holds the current response
     */
    protected $response;

    /**
     * Server services routes
     * 
     * @var array Holds the available URL routes
     */
    protected $routes;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        // Create default response
        $this->setResponse($this->createResponse());

        // Create default routes
        $this->routes = array();
        
        // Set default home
        $this->setHome('\Duality\Structure\Http\Response');
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
     * Add a route
     * 
     * @param string $uriPattern The uri matching pattern
     * @param string $res        The home response
     * @param string $req        The home request
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function addRoute($uriPattern, $res, $req = null)
    {
        App::validateClassname($res);
        App::validateClassname($req);
        $this->routes[$uriPattern] = array(
            'response'  => $res,
            'request'   => $req
        );
        return $this;
    }
    
    /**
     * Sets the home route
     * 
     * @param string $res The home response
     * @param string $req The home request
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function setHome($res, $req = null)
    {
        unset($this->routes['/^\/$/i']);
        $this->addRoute('/^\/$/i', $res, $req);
        return $this;
    }

    /**
     * Starts server and run routes callbacks
     * 
     * @return void
     */
    public function execute()
    {   
        // Set temporary request and response
        $response = $this->getResponse();
        $request = $this->getRequest();
        
        // Start looking for matching routes patterns
        foreach ($this->routes as $ns => $route) {

            // Reload temporary
            $response = $this->getResponse();
            $request = empty($route['request']) ?
                    $this->getRequest()
                    : new $route['request']($this->getRequest()->getUrl());
            
            // Check if route matches and stop looking
            $uri = $request->getUrl()->getUri();
            if (preg_match($ns, $uri, $matches)) {
                $request->setRouteParams(array_shift($matches));
                $response = new $route['response'];
                break;
            }
        }

        // Check for authorization
        $request->isAuthorized($response);
        
        // Set request and response
        $this->setRequest($request);
        $this->setResponse($response);

        // Finally send response
        $this->send($this->getResponse());
    }

    /**
     * Sets the request
     * 
     * @param \Duality\Structure\Http\Request $request Give the current request
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Gets the request
     * 
     * @return \Duality\Structure\Http\Request The current request
     */
    public function getRequest()
    {
        if (empty($this->request)) {
            $this->request = new Request();
            $this->request->importFromGlobals();
        }
        return $this->request;
    }

    /**
     * Sets the response
     * 
     * @param \Duality\Structure\Http\Response $response Give the current response
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Gets the response
     * 
     * @return \Duality\Structure\Http\Response The current response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets the server host name, used to parse request route
     * 
     * @param string $hostname Give the server a name
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
        return $this;
    }

    /**
     * Gets the server host name, used to parse request route
     * 
     * @return string The server hostname
     */
    public function getHostname()
    {
        return $this->hostname;
    }
    
    /**
     * Sets the server base url, used to parse request route
     * 
     * @param string $url Give the server a name
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function setBaseUrl(InterfaceUrl $url)
    {
        $this->baseURL = $url;
        return $this;
    }

    /**
     * Gets the server base url, used to parse request route
     * 
     * @return string The base url
     */
    public function getBaseUrl()
    {
        return (string) $this->baseURL;
    }

    /**
     * Creates a valid server URL
     * 
     * @param string $uri    Give the URI
     * 
     * @return string The resulting URL
     */
    public function createUrl($uri)
    {
        return trim($this->baseURL, '/'). '/' . trim($uri, '/');
    }

    /**
     * Creates an HTTP response
     * 
     * @return \Duality\Structure\Http\Response A default response instance
     */
    public function createResponse()
    {
        return new Response;
    }
    
    /**
     * Creates an HTTP redirect
     * 
     * @since 1.0.1
     * 
     * @param string $uri  The local uri to redirect
     * @param int    $code The HTTP status code (defaults to 301)
     * 
     * @return \Duality\Structure\Http\Response A default response instance
     */
    public function createRedirect($uri = '/', $code = 301)
    {
        $response = $this->createResponse();
        $response->addHeader('Location', $this->createUrl($uri));
        $response->setStatus($code);
        return $response;
    }

    /**
     * Writes HTTP response to application buffer
     * 
     * @param \Duality\Structure\Http $response    Give the server response
     * @param boolean                 $withHeaders Send headers or not
     * 
     * @return \Duality\Service\HTTPServer This HTTP server
     */
    public function send(Response $response, $withHeaders = true)
    {
        if ($withHeaders) {
            $this->sendHeaders($response);
            $this->sendCookies($response);
        }

        $this->app->getBuffer()->write($response->getContent());
        return $this;
    }

    /**
     * Sends HTTP Headers if supported by SAPI
     * 
     * @param \Duality\Structure\Http\Response $response The response to be sent
     * 
     * @return \Duality\Service\Server This instance
     */
    abstract public function sendHeaders(Response $response);

    /**
     * Sends HTTP cookies
     * 
     * @param Response $response The HTTP response
     * 
     * @return \Duality\Service\HTTPServer
     */
    abstract public function sendCookies(Response $response);
    
}