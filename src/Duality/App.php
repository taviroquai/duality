<?php

/**
 * High level application container (DIC)
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality;

use \Duality\Core\DualityException;
use \Duality\Core\Container;
use \Duality\Structure\Storage;
use \Duality\Structure\File\StreamFile;

/**
 * Default application container
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class App 
extends Container
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
        'ssh'       => 'Duality\Service\SSH',
        'server'    => 'Duality\Service\Server',
        'locale'    => 'Duality\Service\Localization',
        'cmd'       => 'Duality\Service\Commander',
        'client'    => 'Duality\Service\Client',
        'performance' => 'Duality\Service\Performance'
    );

    /**
     * Create a new application
     * 
     * @param string $path   Give the base path to resolve relative paths
     * @param array  $config Give the configuration as array
     */
    public function __construct($path, $config)
    {
        if (!is_dir($path)) {
            throw new DualityException(
                "Error Application: path not found",
                DualityException::E_APP_PATHNOTFOUND
            );
        }
        $this->path = (string) $path;

        $config['services'] = empty($config['services']) ?
            $this->defaults : array_merge($this->defaults, $config['services']);
        $this->config = (array) $config;

        $this->services = new Storage;
        $this->services->reset();
        $this->cache = new Storage;
        $this->cache->reset();

        $bufferType = $this->getConfigItem('buffer') ? 
            $this->getConfigItem('buffer') : 'php://output';
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
            if (is_a($instance, 'Duality\Core\AbstractService', true)) {
                call_user_func(array($instance, 'terminate'));
            }
        }
        $this->getBuffer()->close();
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
     * @return \Duality\Service\Logger The logger service
     */
    public function getLogger()
    {
        return $this->call('logger');
    }

    /**
     * Call security service alias (type hinting)
     * 
     * @return \Duality\Service\Security The security service
     */
    public function getSecurity()
    {
        return $this->call('security');
    }

    /**
     * Call validator service alias (type hinting)
     * 
     * @return \Duality\Service\Validator The validator service
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
     * @return \Duality\Service\Mailer The mailer service
     */
    public function getMailer()
    {
        return $this->call('mailer');
    }

    /**
     * Call paginator service alias (type hinting)
     * 
     * @return \Duality\Service\Paginator The paginator service
     */
    public function getPaginator()
    {
        return $this->call('paginator');
    }

    /**
     * Call ssh service alias (type hinting)
     * 
     * @return \Duality\Service\SSH The ssh service
     */
    public function getSSH()
    {
        return $this->call('ssh');
    }

    /**
     * Call http server service alias (type hinting)
     * 
     * @return \Duality\Service\Server The server service
     */
    public function getServer()
    {
        return $this->call('server');
    }

    /**
     * Call locale service alias (type hinting)
     * 
     * @return \Duality\Service\Localization The localization service
     */
    public function getLocale()
    {
        return $this->call('locale');
    }

    /**
     * Call cmd service alias (type hinting)
     * 
     * @return \Duality\Service\Commander The commander service
     */
    public function getCmd()
    {
        return $this->call('cmd');
    }

    /**
     * Call http client alias (type hinting)
     * 
     * @return \Duality\Service\Client The http client service
     */
    public function getClient()
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