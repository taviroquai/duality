<?php

/**
 * High level application container (DIC)
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality;

use Duality\Core\DualityException;
use Duality\Core\Container;
use Duality\Database\SQLite;
use Duality\Database\MySql;
use Duality\File\StreamFile;
use Duality\Service\Localization;
use Duality\Service\Logger;
use Duality\Service\Validator;
use Duality\Service\Session;
use Duality\Service\Auth;
use Duality\Service\Cache;
use Duality\Service\Mailer;
use Duality\Service\Paginator;
use Duality\Service\SSH;
use Duality\Service\Server;

/**
 * Default application container
 */
class App 
extends Container
{
	/**
	 * Holds application working directory
	 * @var string
	 */
	protected $path;

	/**
	 * Holds environment configuration
	 * @var array
	 */
	protected $config;

	/**
	 * Container cache
	 * @var array
	 */
	protected $cache;
    
    /**
     * Holds the application output buffer
     * @var \Duality\File\StreamFile
     */
    protected $buffer;

    /**
     * Setup default services
     * @var array
     */
	protected $defaults = array(
        'logging'   => 'Duality\Service\Logger',
        'validator' => 'Duality\Service\Validator',
        'session'   => 'Duality\Service\Session',
        'auth'      => 'Duality\Service\Auth',
        'cache'     => 'Duality\Service\Cache',
        'mailer'    => 'Duality\Service\Mailer',
        'paginator' => 'Duality\Service\Paginator',
        'ssh'       => 'Duality\Service\SSH',
        'server'    => 'Duality\Service\Server',
        'locale' 	=> 'Duality\Service\Localization',
        'cmd' 		=> 'Duality\Service\Commander'
    );

	/**
	 * Create a new application
	 * @param string $path
	 * @param array $config
	 */
	public function __construct($path, $config)
	{
		$this->path = $path;
		$this->config = $config;
        $this->services = array();
        $this->buffer = new StreamFile(
            $this->getConfigItem('buffer') ? 
            $this->getConfigItem('buffer') :
            'php://output'
        );
        $this->buffer->open();

		$me =& $this;

		// Set script end hook
		register_shutdown_function(function() use ($me) {
			foreach($me->getServices() as $name => $service) {
				$me->call($name)->terminate();
			}
            $me->getBuffer()->close();
		});
	}

    /**
     * Add common services
     */
	public function loadService($name)
	{
		$me =& $this;

		// Register database
		if ($name === 'db') {
			if ($this->getConfigItem('db.dsn')) {
				$this->defaults['db'] = (strpos($this->getConfigItem('db.dsn'), 'mysql') === 0) ?
					'Duality\Database\MySql' : 
					'Duality\Database\SQLite';
			}	
		}

		// Verify if default service exists
		if (!isset($this->defaults[$name])) {
			throw new DualityException("Default service not found: " . $name, 15);
		}

		// Finally, register and init service
		$class = $this->defaults[$name];
		$this->register($name, function() use ($class, $me) {
            return new $class($me);
        });
        $this->call($name)->init();
	}

	/**
	 * Returns application path
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns environment configuration
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}
    
    /**
	 * Returns application output buffer
	 * @return \Duality\File\StreamFile;
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * Returns environment configuration
	 * @param string $path
	 * @return string|int
	 */
	public function getConfigItem($path)
	{
		$parts = explode('.', $path);
		$result = $this->config;
		foreach ($parts as $item) {
			if (!isset($result[$item])) {
				return false;
			}
			$result = $result[$item];
		}
		return $result;
	}

	/**
	 * Register service
	 * @param string $name
	 * @param \Closure $service
	 * @return Duality\App
	 */
	public function register($name, \Closure $service)
	{
		$this->services[$name] = $service;
		return $this;
	}

	/**
	 * Checks wether exists a regitered service name
	 * @param string $name
	 * @return boolean
	 */
	public function exists($name)
	{
		return isset($this->cache[$name]);
	}

	/**
	 * Call service
	 * @param string $name
	 * @param array $params
	 * @param boolean $cache
	 * @return mixed
	 */
	public function call($name, $params = array(), $cache = true)
	{
		if (!isset($this->services[$name])) {
			$this->loadService($name);
		}
		if ($cache) {
			if (!isset($this->cache[$name])) {
				$this->cache[$name] = call_user_func_array($this->services[$name], $params);
			}
			return $this->cache[$name];
		}
		return call_user_func_array($this->services[$name], $params);
	}

	/**
	 * Translate Helper
	 * @param string $key
	 * @param array $params
	 * @param string $target
	 * @return string
	 */
	public function t($key, $params = array(), $target = NULL)
	{
		return $this->call('locale')->translate($key, $params, $target);
	}

	/**
	 * Security Helper
	 * @param string $data
	 * @return string
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
			if (
				$this->getConfigItem('security.algo') 
				&& in_array($this->getConfigItem('security.algo'), hash_algos())
			) {
				$algo = $this->getConfigItem('security.algo');
			}
		}
		return hash($algo, $data . $salt);
	}
}