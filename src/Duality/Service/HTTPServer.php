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
use Duality\Core\InterfaceAuthorization;
use Duality\Structure\Url;
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;

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
     * Holds the default controller when no route is matched
     * 
     * @var \Duality\Service\UserController Holds the default controller
     */
    protected $defaultController;

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
        $this->setDefault('\Duality\Service\Controller\Base@doIndex');
        
        // Load default request
        if ($request = $this->getRequestFromGlobals($_SERVER, $_REQUEST)) {
            $this->setRequest($request);
        }
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
     * Adds a service route to the server
     * 
     * @param string   $uriPattern Give the URI pattern as route identifier
     * @param \Closure $cb         The route callback
     * 
     * @return void
     */
    public function addRoute($uriPattern, $cb)
    {
        $this->routes[$uriPattern] = $cb;
    }

    /**
     * Sets a default callback to the server when no route is matched
     * 
     * @param \Closure $cb Sets the default callback
     * 
     * @return void
     */
    public function setDefault($cb)
    {
        $this->defaultController =  $cb;
    }
    
    /**
     * Sets the home callback
     * 
     * @param \Closure $cb Give the home callback
     * 
     * @return void
     */
    public function setHome($cb)
    {
        $this->addRoute('/^\/$/i', $cb);
    }    

    /**
     * Starts server and run routes callbacks
     * 
     * @return void
     */
    public function execute()
    {
        // Set default values
        $result = false;
        $matches = array();
        $authorized = true;

        if (!empty($this->request)) {
            
            // Start looking for matching routes patterns
            foreach ($this->routes as $ns => $cb) {
                // Check if route matches and stop looking
                $uri = str_replace(
                    (string) $this->baseURL->getUri(), '', $this->request->getUrl()->getUri()
                );
                $uri = '/' . trim($uri, '/ ');
                if ($result = preg_match($ns, $uri, $matches)) {
                    array_shift($matches);
                    $cb = is_string($cb) ? $this->validateStringAction($cb) : $cb;
                    break;
                }
            }
            
            // No route matches. Call default controller
            if (!$result) {
                $cb = is_string($this->defaultController) ? 
                    $this->validateStringAction($this->defaultController) : 
                    $this->defaultController;
            }
            
            // Call controller init
            if (is_array($cb) 
                && is_object($cb[0]) 
                && ($cb[0] instanceof AbstractService)
            ) {
                $cb[0]->init();
                
                // Check for authorization
                if ($cb[0] instanceof InterfaceAuthorization) {
                    $authorized = $cb[0]->isAuthorized(
                        $this->request, $this->response, $matches
                    );
                }
            }

            // Finally, call action
            if ($authorized) {
                call_user_func_array(
                    $cb, array(&$this->request, &$this->response, $matches)
                );
            }
        }

        $this->send($this->response);
    }
    
    /**
     * Translates route callback to callable
     * 
     * @param string $cb Give the callback to validate
     * 
     * @throws DualityException If fails, throws exception
     * 
     * @return array The valid and callable callback
     */
    protected function validateStringAction($cb)
    {   
        // Translate
        @list($controllerClass, $method) = explode('@', $cb, 2);
        
        // Validate class name
        if (!class_exists($controllerClass)) {
            throw new DualityException(
                "Error Route: controller not found: ".$controllerClass,
                DualityException::E_SERVER_CTRLNOTFOUND
            );
        }
        $controller = new $controllerClass($this->app);
        $action = array($controller, $method);

        // Validate callable
        if (!is_callable($action)) {
            throw new DualityException(
                "Error Route: action not callable: ".$cb,
                DualityException::E_SERVER_ACTIONNOTFOUND
            );
        }
        return $action;
    }

    /**
     * Sets the request
     * 
     * @param \Duality\Structure\Http\Request $request Give the current request
     * 
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets the request
     * 
     * @return \Duality\Structure\Http\Request The current request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the response
     * 
     * @param \Duality\Structure\Http\Response $response Give the current response
     * 
     * @return void
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
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
     * @return void
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
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
     * @return void
     */
    public function setBaseUrl(InterfaceUrl $url)
    {
        $this->baseURL = $url;
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
     * @return void
     */
    public function send(Response $response, $withHeaders = true)
    {
        $sapi_type = php_sapi_name();
        $no_support = array('cli', 'cli-server');
        if ($withHeaders && !in_array($sapi_type, $no_support)) {
            $this->sendHeaders($response);
            $this->sendCookies($response);
        }

        $this->app->getBuffer()->write($response->getContent());
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
            header($k.': '.$v);
        }
        return $this;
    }

    /**
     * Sets an HTTP cookie
     * 
     * @param \Duality\Structure\Http\Response $response The response to be sent
     * 
     * @throws \Duality\Core\DualityException When finds an invalid cookie
     * 
     * @return \Duality\Service\Server This instance
     */
    public function sendCookies(Response $response)
    {
        $required = array('name', 'value', 'expire', 'path', 'domain', 'secure');
        
        foreach ($response->getCookies() as $item) {

            // Validate cookie
            $hasKeys = array_intersect_key(array_flip($required), $item);
            if (count($hasKeys) !== count($required)) {
                throw new DualityException(
                    "Error HTTP Cookie: required keys: "
                    . "name, value, expire, path, domain and secure",
                    DualityException::E_HTTP_INVALIDCOOKIE
                );
            }

            // send cookie
            setcookie(
                $item['name'],
                $item['value'],
                $item['expire'],
                $item['path'],
                $item['domain'],
                $item['secure']
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