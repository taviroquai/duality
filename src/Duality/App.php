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
use \Duality\Service\Database\SQLite;
use \Duality\Service\Database\MySql;
use \Duality\Structure\File\StreamFile;
use \Duality\Service\Localization;
use \Duality\Service\Logger;
use \Duality\Service\Validator;
use \Duality\Service\Session;
use \Duality\Service\Auth;
use \Duality\Service\Cache;
use \Duality\Service\Mailer;
use \Duality\Service\Paginator;
use \Duality\Service\SSH;
use \Duality\Service\Server;

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
        'logger'    => 'Duality\Service\Logger',
        'validator' => 'Duality\Service\Validator',
        'session'   => 'Duality\Service\Session',
        'auth'      => 'Duality\Service\Auth',
        'cache'     => 'Duality\Service\Cache',
        'mailer'    => 'Duality\Service\Mailer',
        'paginator' => 'Duality\Service\Paginator',
        'ssh'       => 'Duality\Service\SSH',
        'server'    => 'Duality\Service\Server',
        'locale'    => 'Duality\Service\Localization',
        'cmd'       => 'Duality\Service\Commander'
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
        $this->config = (array) $config;

        $this->services = new Storage;
        $this->services->reset();
        $this->cache = new Storage;
        $this->cache->reset();
        
        if ($this->getConfigItem('buffer')) {
            $this->buffer = new StreamFile(
                $this->getConfigItem('buffer') ? 
                $this->getConfigItem('buffer') :
                'php://stdout'
            );
            $this->buffer->open();
        }
    }

    public function __destruct()
    {
        foreach ($this->getServices() as $name => $service) {
            $this->call($name)->terminate();
        }
        if ($this->getBuffer()) {
            $this->getBuffer()->close();
        }
    }

    /**
     * Add common services
     * 
     * @param string $name Give the name to load the service
     * 
     * @return void
     */
    public function loadService($name)
    {
        $me =& $this;

        // Register database
        if ($name === 'db') {
            if (!$this->getConfigItem('db.dsn')) {
                throw new DualityException("Error configuration: db.dsn not found ", 2);
            }
            if ($this->getConfigItem('db.dsn')) {
                $isMysql = strpos($this->getConfigItem('db.dsn'), 'mysql') === 0;
                $this->defaults['db'] = $isMysql ?
                    'Duality\Service\Database\MySql' : 
                    'Duality\Service\Database\SQLite';
            }   
        }

        // Verify if default service exists
        if (!isset($this->defaults[$name])) {
            throw new DualityException("Default service not found: " . $name, 15);
        }

        // Finally, register and init service
        $class = $this->defaults[$name];
        $this->register(
            $name, function () use ($class, $me) {
                return new $class($me);
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
        if (!$this->services->has($name)) {
            $this->loadService($name);
        }
        if ($cache) {
            if (!$this->cache->has($name)) {
                $this->cache->set(
                    $name, call_user_func_array($this->services->get($name), $params)
                );
            }
            return $this->cache->get($name);
        }
        return call_user_func_array($this->services->get($name), $params);
    }

    /**
     * Translate Helper
     * 
     * @param string $key    Give the message key
     * @param array  $params Give the message values
     * @param string $target Give the target locale
     * 
     * @return string The translated message
     */
    public function t($key, $params = array(), $target = null)
    {
        return $this->call('locale')->translate($key, $params, $target);
    }

    /**
     * Security Helper
     * 
     * @param string $data Give the data to be encrypt
     * 
     * @return string The resulting encrypted data
     */
    public function encrypt($data)
    {
        // Set defaults
        $algo = 'sha256';
        $salt = '';

        // Apply user configuration if exists
        if ($this->getConfigItem('security')) {
            if ($this->getConfigItem('security.salt')) {
                $salt = $this->getConfigItem('security.salt');
            }
            if ($this->getConfigItem('security.algo') 
                && in_array($this->getConfigItem('security.algo'), hash_algos())
            ) {
                $algo = $this->getConfigItem('security.algo');
            }
        }
        return hash($algo, (string) $data . $salt);
    }
}