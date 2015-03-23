<?php

/**
 * High level application container (DIC)
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality;

use Duality\Core\AbstractContainer;
use Duality\Core\AbstractService;
use Duality\Core\InterfaceAuthentication;
use Duality\Structure\Storage;
use Duality\Structure\File\StreamFile;

/**
 * Default application container
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
class App 
extends AbstractContainer
implements InterfaceAuthentication
{
    /**
     * Holds application working directory
     * 
     * @var string The base path of the application
     */
    protected $path;

    /**
     * Holds environment configuration
     * 
     * @var array The original configuration
     */
    protected $config;

    /**
     * Container cache
     * 
     * @var \Duality\Core\InterfaceStorage The cache storage
     */
    protected $cache;
    
    /**
     * Holds the application output buffer
     * 
     * @var \Duality\File\StreamFile The output buffer
     */
    protected $buffer;
    
    /**
     * Holds the authentication key
     * 
     * @var string The authentication key;
     */
    protected $authKey = '__auth';

    /**
     * Setup default services
     * 
     * @var array The default Duality services
     */
    protected $defaults = array(
        'db'        => 'Duality\Service\Database\SQLite',
        'logger'    => 'Duality\Service\Logger',
        'security'  => 'Duality\Service\Security',
        'validator' => 'Duality\Service\Validator',
        'session'   => 'Duality\Service\Session\Dummy',
        'auth'      => 'Duality\Service\Auth\Database',
        'cache'     => 'Duality\Service\Cache\APC',
        'mailer'    => 'Duality\Service\Mailer',
        'paginator' => 'Duality\Service\Paginator',
        'remote'    => 'Duality\Service\SSH',
        'server'    => 'Duality\Service\HTTPServer',
        'idiom'     => 'Duality\Service\Translation',
        'cmd'       => 'Duality\Service\Commander',
        'client'    => 'Duality\Service\HTTPClient',
        'performance' => 'Duality\Service\Performance'
    );

    /**
     * Create a new application
     * 
     * @param array  $config Give the configuration as array
     */
    public function __construct($config = array())
    {
        $this->path = getcwd();

        $config['services'] = empty($config['services']) ?
            $this->defaults :
            array_merge($this->defaults, $config['services']);
        $this->config = (array) $config;

        $this->services = new Storage;
        $this->cache = new Storage;

        $bufferType = $this->getConfigItem('buffer') ? 
            $this->getConfigItem('buffer') :
            'php://output';
        $this->buffer = new StreamFile($bufferType);
        $this->buffer->open();
    }

    /**
     * Terminate services and close buffer (if exists)
     * 
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->cache->asArray() as $name => $service) {
            $instance = $this->call($name);
            if ($instance instanceof AbstractService) {
                call_user_func(array($instance, 'terminate'));
            }
        }
        $this->getBuffer()->close();
    }
    
    /**
     * Validates user classname
     * 
     * @param string $name The classname
     * 
     * @throws \Exception
     */
    static public function validateClassname($name)
    {
        if (is_string($name) && !class_exists($name)) {
            throw new \Exception('Invalid class name: ' . $name);
        }
    }

    /**
     * Add common services
     * 
     * @param string $name Give the name to load the service
     * 
     * @return void
     */
    protected function loadService($name)
    {
        $me =& $this;

        // Register and init service
        $class = $this->config['services'][$name];
        $this->register(
            $name, function () use ($class, $me) {
                $instance = new $class($me);
                return $instance;
            }
        );
        $service = $this->call($name);
        if (is_a($service, 'Duality\Core\AbstractService', true)) {
            $service->init();
        }
    }

    /**
     * Returns application path
     * 
     * @return string The base path to resolve relative paths
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns environment configuration
     * 
     * @return array The original configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Returns application output buffer
     * 
     * @return \Duality\File\StreamFile The output buffer
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Returns environment configuration
     * 
     * @param string $path Give the path, ie. mailer.smtp.pass
     * 
     * @return mixed|null The result value or null
     */
    public function getConfigItem($path)
    {
        $parts = explode('.', $path);
        $result = $this->config;
        foreach ($parts as $item) {
            if (!isset($result[$item])) {
                return null;
            }
            $result = $result[$item];
        }
        return $result;
    }
    
    /**
     * Returns environment configuration
     * 
     * @param string $path Give the path, ie. mailer.smtp.pass
     * 
     * @return mixed|null The result value or null
     */
    public function cfg($path)
    {
        return $this->getConfigItem($path);
    }

    /**
     * Register service
     * 
     * @param string   $name    Give a name to the service
     * @param \Closure $service Give the service callback
     * 
     * @return \Duality\App This instance
     */
    public function register($name, \Closure $service)
    {
        $this->services->set($name, $service);
        return $this;
    }

    /**
     * Checks wether exists a regitered service name
     * 
     * @param string $name Give a name to check if service exists
     * 
     * @return boolean The check result
     */
    public function exists($name)
    {
        return $this->cache->has($name);
    }

    /**
     * Call service
     * 
     * @param string  $name   Give a name to identify the service
     * @param array   $params Give the parameters to pass
     * @param boolean $cache  Tell whether to cache the callback result or not
     * 
     * @return mixed The service callback result
     */
    public function call($name, $params = array(), $cache = true)
    {
        if (!$this->services->has($name)
            && isset($this->config['services'][$name])
        ) {
            $this->loadService($name);
        }
        if ($cache) {
            if (!$this->exists($name)) {
                $this->cache->set(
                    $name, call_user_func_array($this->services->get($name), $params)
                );
            }
            return $this->cache->get($name);
        }
        return call_user_func_array($this->services->get($name), $params);
    }
    
    /**
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password)
    {
        $result = false;
        if ($this->call('auth')->login($username, $password)) {
            $this->call('session')->set($this->authKey, $username);
            $result = true;
        }
        return $result;
    }
    
    /**
     * Check if there is a user logged
     * 
     * @return boolean Tells whether the user is logged or not
     */
    public function isLogged()
    {
        return $this->call('session')->has($this->authKey);
    }

    /**
     * Logs a user out
     * 
     * @return void
     */
    public function logout()
    {
        $this->call('session')->reset();
    }

    /**
     * Returns the current logged user
     * 
     * @return string The current logged username
     */
    public function whoAmI()
    {
        return $this->call('session')->get($this->authKey);
    }

    /**
     * Call database service alias (type hinting)
     * 
     * @return \Duality\Service\Database The database service
     */
    public function getDb()
    {
        return $this->call('db');
    }

    /**
     * Call logger service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceErrorHandler The logger service
     */
    public function getLogger()
    {
        return $this->call('logger');
    }

    /**
     * Call security service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceSecurity The security service
     */
    public function getSecurity()
    {
        return $this->call('security');
    }

    /**
     * Call validator service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceValidator The validator service
     */
    public function getValidator()
    {
        return $this->call('validator');
    }

    /**
     * Call session service alias (type hinting)
     * 
     * @return \Duality\Service\Session The session service
     */
    public function getSession()
    {
        return $this->call('session');
    }

    /**
     * Call auth service alias (type hinting)
     * 
     * @return \Duality\Service\Auth The auth service
     */
    public function getAuth()
    {
        return $this->call('auth');
    }

    /**
     * Call cache service alias (type hinting)
     * 
     * @return \Duality\Service\Cache The cache service
     */
    public function getCache()
    {
        return $this->call('cache');
    }

    /**
     * Call mailer service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceMailer The mailer service
     */
    public function getMailer()
    {
        return $this->call('mailer');
    }

    /**
     * Call paginator service alias (type hinting)
     * 
     * @return \Duality\Core\InterfacePaginator The paginator service
     */
    public function getPaginator()
    {
        return $this->call('paginator');
    }

    /**
     * Call remote service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceRemote The remote service
     */
    public function getRemote()
    {
        return $this->call('remote');
    }

    /**
     * Call http server service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceServer The server service
     */
    public function getHTTPServer()
    {
        return $this->call('server');
    }

    /**
     * Call translation service alias (type hinting)
     * 
     * @return \Duality\Core\Interfaceslation The translation service
     */
    public function getIdiom()
    {
        return $this->call('idiom');
    }

    /**
     * Call cmd service alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceCommander The commander service
     */
    public function getCmd()
    {
        return $this->call('cmd');
    }

    /**
     * Call http client alias (type hinting)
     * 
     * @return \Duality\Core\InterfaceClient The http client service
     */
    public function getHTTPClient()
    {
        return $this->call('client');
    }

    /**
     * Call performance alias (type hinting)
     * 
     * @return \Duality\Service\Performance The performance service
     */
    public function getPerformance()
    {
        return $this->call('performance');
    }
}