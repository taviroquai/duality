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
use Duality\Structure\Url;
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
class HTTPServer
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
     * Server host name
     * 
     * @var string Holds the server hostname
     */
    protected $hostname;

    /**
     * Server base URL
     * 
     * @var \Duality\Core\InterfaceUrl Holds the base URL used to parse routes
     */
    protected $baseURL;

    /**
     * Server services routes
     * 
     * @var array Holds the available URL routes
     */
    protected $routes;
    
    /**
     * Holds the unsupported asapi names
     * 
     * @var array
     */
    protected $noSupport = array('cli', 'cli-server');

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->hostname = gethostname();
        $this->setBaseUrl(new Url('http://'.$this->hostname));

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
        $route = $this->findRouteMatch();   
        if ($route) {

            // Get user request
            if (!empty($route['request'])) {
                $request = new $route['request'];
                $request->import($this->getRequest());
                $request->setRouteParams($route['params']);
                $this->setRequest($request);
            }

            // Set response
            $this->setResponse(new $route['response']);

            // Check for authorization
            if ($this->request->isAuthorized()) {
                $this->response->onRequest($this->request);
            } else {
                $this->setResponse($this->request->onUnauthorized());
            }
        }

        $this->send($this->response);
    }
    
    /**
     * Finds a matchable route
     * 
     * @return boolean
     */
    protected function findRouteMatch()
    {
        // Start looking for matching routes patterns
        foreach ($this->routes as $ns => $route) {

            // Check if route matches and stop looking
            $uri = $this->getRequest()->getUrl()->getUri();
            $uri = str_replace((string) $this->baseURL->getUri(), '', $uri);
            $uri = '/' . trim($uri, '/ ');
            if (preg_match($ns, $uri, $matches)) {
                $route['params'] = array_shift($matches);
                return $route;
            }
        }
        return false;
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
            $this->request = $this->getRequestFromGlobals($_SERVER, $_REQUEST);
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

    /**
     * Parses HTTP properties from PHP global environment
     * 
     * @param array $server The global $_SERVER variable
     * @param array $params The global $_REQUEST/$_GET/$_POST variable
     * 
     * @return Request The resulting request instance
     */
    public function getRequestFromGlobals($server, $params)
    {
        if (empty($server['REQUEST_METHOD'])) {
            return false;
        }
        
        // Filter input
        array_filter($server, function(&$var) {
            $var = filter_var($var, FILTER_UNSAFE_RAW);
        });
        array_filter($params, function(&$var) {
            $var = filter_var($var, FILTER_UNSAFE_RAW);
        });
        
        // Detect base URL and URI
        $server['SERVER_NAME'] = empty($server['SERVER_NAME']) ? 
                $this->hostname : $server['SERVER_NAME'];
        $server['SCRIPT_NAME'] = empty($server['SCRIPT_NAME']) ? 
                '/index.php' : $server['SCRIPT_NAME'];
        $server['REQUEST_URI'] = empty($server['REQUEST_URI']) ?
                '/' : $server['REQUEST_URI'];
        $baseUrl = (empty($server['HTTPS']) ? 'http' : 'https')
            . "://"
            . $server['SERVER_NAME']
            . dirname($server['SCRIPT_NAME']);
        $uri = $server['REQUEST_URI'];
        $uri = str_replace(dirname($server['SCRIPT_NAME']), '', $uri);
        $uri = str_replace(basename($server['SCRIPT_NAME']), '', $uri);
        $uri = '/' . trim($uri, '/');
        
        // Set base URL and URI strings
        $this->setBaseUrl(new Url($baseUrl));
        $request = new Request(new Url($baseUrl . $uri));
        $request->setMethod($server['REQUEST_METHOD']);
        $request->setContent(file_get_contents('php://input'));
        $request->setTimestamp(
            empty($server['REQUEST_TIME']) ? time() : $server['REQUEST_TIME']
        );
        $headers = array(
            'Http-Accept'           => empty($server['HTTP_ACCEPT']) ? 
                'text/html' : $server['HTTP_ACCEPT'],
            'Http-Accept-Language'  => !empty($server['HTTP_ACCEPT_LANGUAGE']) ?
                $server['HTTP_ACCEPT_LANGUAGE'] : 'en-US',
            'Http-Accept-Charset'   => !empty($server['HTTP_ACCEPT_CHARSET']) ? 
                $server['HTTP_ACCEPT_CHARSET'] : 
                !empty($server['HTTP_ACCEPT_ENCODING']) ? 
                $server['HTTP_ACCEPT_ENCODING'] : 'utf-8',
            'Http-Host'             => empty($server['REMOTE_HOST']) ? 
                empty($server['REMOTE_ADDR']) ? '' : $server['REMOTE_ADDR']
                : $server['REMOTE_HOST'],
            'Referer'               => empty($server['REFERER']) ? 
                '' : $server['REFERER']
        );
        $request->setHeaders($headers);
        $request->setParams($params);

        if (!empty($server['HTTP_X_REQUESTED_WITH']) 
            && strtolower($server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $request->setAjax(true);
        }
        return $request;
    }
}