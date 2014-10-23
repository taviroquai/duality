<?php

/**
 * High level application container (DIC)
 *
 * PHP Version 5.3.3
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
 * PHP Version 5.3.3
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
        'auth'      => 'Duality\Service\Auth',
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
            throw new DualityException("Error Application: path not found", 1);
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
        $this->call($name)->init();
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
            && isset($this->defaults[$name])
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
}