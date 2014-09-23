<?php

namespace Duality\System;

use Duality\System\Core\DualityException;
use Duality\System\Core\Container;
use Duality\System\Service\Logger;
use Duality\System\Service\Session;
use Duality\System\Service\Cache;
use Duality\System\Service\Mailer;
use Duality\System\Service\Auth;
use Duality\System\Service\Localization;
use Duality\System\Service\Paginator;
use Duality\System\Service\SSH;
use Duality\System\Service\Server;
use Duality\System\Database\SQLite;
use Duality\System\Database\MySql;

/**
 * Default application container
 */
class App extends Container
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
	 * Create a new application
	 */
	public function __construct($path, $config)
	{
		$this->path = $path;
		$this->config = $config;
        $this->services = array();

		$me =& $this;

		// Set script end hook
		ob_start();
		register_shutdown_function(function() use ($me) {
			foreach($me->getServices() as $name => $service) {
				if (method_exists($service, 'terminate')) {
					$me->call($name)->terminate();	
				}
			}
			echo ob_get_clean();
		});
	}

	public function initDefaultServices()
	{
		$me =& $this;
		$config = $this->getConfig();

		// Register default logger
		$this->register('logging', function() use ($me) {
			return new Logger($me);
		});

		// Register database
		if (isset($config['db_dsn'])) {
			$me->register('db', function () use ($me) {
				$config = $me->getConfig();
			    if (strpos($config['db_dsn'], 'sqlite') === 0) {
			    	$db = new SQLite($me);
			    } else {
			    	$db = new MySql($me);
			    }
			    return $db;
			});	
		}

		// Register default session handler
		$this->register('session', function() use ($me) {
			return new Session($me);
		});

		// Register default authentication storage
		$this->register('auth', function() use ($me) {
			return new Auth($me);
		});

		// Register default cache storage
		$this->register('cache', function() use ($me) {
			return new Cache($me);
		});

		// Register default localization storage
		$this->register('i18n', function() use ($me) {
			return new Localization($me);
		});

		// Register default mailer
		$this->register('mailer', function() use ($me) {
			return new Mailer($me);
		});

		// Register default paginator
		$this->register('paginator', function() use ($me) {
			return new Paginator($me);
		});

		// Register default ssh service
		$this->register('ssh', function() use ($me) {
			return new SSH($me);
		});

		// Register default http server
		$this->register('server', function() use ($me) {
			return new Server($me);
		});

		// init services
		foreach($this->services as $name => $service) {
			$fn = array($service, 'init');
			if (is_callable($fn, true)) {
				$this->call($name)->init();
			}
		}
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
	 * Register service
	 * @param string $name
	 * @return Duality\System\App
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
			throw new DualityException("Service undefined: " . $name, 15);
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
	 * @return string
	 */
	public function t($key, $params = array())
	{
		return $this->call('i18n')->translate($key, $params);
	}

	/**
	 * Security Helper
	 * @param string $value
	 * @return string
	 */
	public function encrypt($data)
	{
		// Set defaults
		$algo = 'sha256';
		$salt = '';

		// Apply user configuration if exists
		$config = $this->getConfig();
		if (isset($config['security'])) {
			if (isset($config['security']['salt'])) {
				$salt = $config['security']['salt'];
			}
			if (isset($config['security']['algo']) && in_array($config['security']['algo'], hash_algos())) {
				$algo = $config['security']['algo'];
			}
		}
		return hash($algo, $data . $salt);
	}
}