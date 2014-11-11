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
     * @var \Duality\Structure\Url Holds the base URL used to parse routes
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
     * Creates a valid server URL
     * 
     * @param string $uri    Give the URI
     * @param string $scheme Give the HTTP scheme/protocol
     * 
     * @return string The resulting URL
     */
    public function createUrl($uri, $scheme = 'http')
    {
        return new URL($scheme.'://'.$this->getHostname(). '/' . trim($uri, '/'));
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
     * @throws DualityException When cookie is invalid
     * 
     * @return \Duality\Service\Server This instance
     */
    public function sendCookies(Response $response)
    {
        foreach ($response->getCookies() as $item) {
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
     * @return Request|false The resulting request instance
     */
    public function getRequestFromGlobals($server, $params)
    {
        if (empty($server['REQUEST_METHOD'])) {
            return false;
        }

        $url = (empty($server['HTTPS']) ? 'http' 
            : 'https') . "://"
            . (empty($server['HTTP_HOST']) ? 
                $this->getHostname() : $server['HTTP_HOST'])
            . (empty($server['REQUEST_URI']) ? '/' : $server['REQUEST_URI']);
        
        $request = new Request(new Url($url));
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