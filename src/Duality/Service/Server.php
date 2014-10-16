<?php

/**
 * HTTP server
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Structure\Url;
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;

/**
 * Simulates an HTTP server
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Server
extends AbstractService
{
    /**
     * Default request
     * 
     * @var string Holds the current request
     */
    protected $request;

    /**
     * Default response
     * 
     * @var string Holds the current response
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
     * @var string Holds the base URL used to parse routes
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
        $this->hostname = $this->app->getConfigItem('server.hostname') ? 
            gethostname() : 
            $this->app->getConfigItem('server.hostname');
        $url = $this->app->getConfigItem('server.url') ? 
            $this->app->getConfigItem('server.url') : 
            '/';
        $this->baseURL = new Url($url);

        // Create default request and response
        // $this->setRequest($this->getRequestFromGlobals());
        $this->setResponse($this->createResponse());

        // Create default routes
        $this->routes = array();
        $this->setDefault('\Duality\Service\Controller\Base@doIndex');
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
    public function listen()
    {
        // Set default values
        $result = false;
        $matches = array();

        if (!empty($this->request)) {
            
            // Start looking for matching routes patterns
            foreach ($this->routes as $ns => $cb) {
                
                // Check if route matches and stop looking
                $uri = str_replace(
                    (string) $this->baseURL, '', $this->request->getUrl()->getUri()
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
            
            // Finally, call controller
            call_user_func_array(
                $cb, array(&$this->request, &$this->response, $matches)
            );
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
        @list($controllerClass, $method) = explode('@', $cb);
        
        // Validate class name
        if (!class_exists($controllerClass)) {
            throw new DualityException(
                "Error Route: controller not found: ".$controllerClass, 2
            );
        }
        $controller = new $controllerClass($this->app);
        $controller->init();
        $action = array($controller, $method);

        // Validate callable
        if (!is_callable($action)) {
            throw new DualityException(
                "Error Route: action not callable: ".$cb, 3
            );
        }
        return $action;
    }

    /**
     * Sets the request
     * 
     * @param Request $request Give the current request
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
     * @return Request The current request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets the response
     * 
     * @param Response $response Give the current response
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
     * @return Request The current request
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
     * Creates a valid server URL
     * 
     * @param string $uri    Give the URI
     * @param string $scheme Give the HTTP scheme/protocol
     * 
     * @return string The resulting URL
     */
    public function createUrl($uri, $scheme = 'http')
    {
        return new URL($scheme.'://'.$this->getHostname().$uri);
    }

    /**
     * Creates an HTTP response
     * 
     * @return \Duality\Structure\Http A default response instance
     */
    public function createResponse()
    {
        return new Response;
    }

    /**
     * Echos HTTP response
     * 
     * @param \Duality\Structure\Http $response    Give the server response
     * @param boolean                 $withHeaders Send headers or not
     * 
     * @return void
     */
    public function send(Response $response, $withHeaders = true)
    {
        if ($withHeaders) {
            $response->sendHeaders();
        }

        $this->app->getBuffer()->write($response->getContent());
    }

    /**
     * Parses HTTP properties from PHP global environment
     * 
     * @return Request The resulting request instance
     */
    public function getRequestFromGlobals()
    {
        if (empty($_SERVER['REQUEST_METHOD'])) {
            return false;
        }

        $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . 
            "://"
            . (empty($_SERVER['HTTP_HOST']) ? $this->getHostname() : $_SERVER['HTTP_HOST'])
            . (empty($_SERVER['REQUEST_URI']) ? '/' : $_SERVER['REQUEST_URI']);
        
        $request = new Request(new Url($url));
        $request->setMethod($_SERVER['REQUEST_METHOD']);
        $request->setContent(file_get_contents('php://input'));
        $request->setTimestamp($_SERVER['REQUEST_TIME']);
        $headers = array(
            'Http-Accept'           => $_SERVER['HTTP_ACCEPT'],
            'Http-Accept-Language'  => !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ?
                $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en-US',
            'Http-Accept-Charset'   => !empty($_SERVER['HTTP_ACCEPT_CHARSET']) ? 
                $_SERVER['HTTP_ACCEPT_CHARSET'] : 
                !empty($_SERVER['HTTP_ACCEPT_ENCODING']) ? 
                $_SERVER['HTTP_ACCEPT_ENCODING'] : 'utf-8',
            'Http-Host'             => empty($_SERVER['REMOTE_HOST']) ? 
                $_SERVER['REMOTE_ADDR'] : $_SERVER['REMOTE_HOST'],
            'Referer'               => empty($_SERVER['REFERER']) ? '' : 
                $_SERVER['REFERER']
        );
        $request->setHeaders($headers);
        $request->setParams($_REQUEST);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        ) {
            $request->setAjax(true);
        }
        return $request;
    }
}