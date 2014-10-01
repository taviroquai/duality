<?php

/**
 * Localization service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\InterfaceService;
use Duality\Core\InterfaceStorage;
use Duality\App;

/**
 * Default localization service
 * 
 * TODO: Add timezone to list
 */
class Localization 
implements InterfaceStorage, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\App
	 */
	protected $app;

	/**
	 * Holds the directory for translations
	 * @var string
	 */
	protected $directory = './data/lang/';

	/**
	 * Holds the messages storage
	 * @var array
	 */
	protected $storage;

	/**
	 * Holds the current iso localization parameters
	 * @var string
	 */
	protected $current;

	/**
	 * Creates a new error handler
	 * @param Duality\App $app
	 */
	public function __construct(App &$app)
	{
		$this->app = & $app;
		$this->storage = array();
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
		if (!extension_loaded('intl')) {
			throw new DualityException("Error: intl extension not loaded", 1);
		}
		if ($this->app->getConfigItem('i18n.default') == NULL) {
			throw new DualityException("Error: i18n configuration missing", 2);
		}
		if ($this->app->getConfigItem('i18n.dir')) {
			$this->directory = $this->app->getConfigItem('i18n.dir');
		}
		if (!is_dir($this->directory) || !is_readable($this->directory)) {
			throw new DualityException("Error: directory not readable: " . $this->directory, 3);
		}
		$request = $this->app->call('server')->getRequest();
		$this->current = \Locale::acceptFromHttp(
			$request->getHeaderItem('Http-Accept-Language')
		);
		if (is_null($this->current)) {
			$this->current = $this->app->getConfigItem('i18n.default');
		}
		$this->setLocale($this->current);
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{

	}

	/**
	 * Loads localizations params
	 * @param string $code
	 */
	public function setLocale($code)
	{
		$code = \Locale::canonicalize($code);
		$this->current = $code;
		if (
			\Locale::acceptFromHttp($code) === NULL
			|| !is_dir($this->directory.DIRECTORY_SEPARATOR.$code)
		) {
			$this->current = \Locale::canonicalize(
				$this->app->getConfigItem('i18n.default')
			);
		}
		\Locale::setDefault($this->current);
		$directory = $this->directory.DIRECTORY_SEPARATOR.$this->current;
		$this->storage = include($directory.DIRECTORY_SEPARATOR.'messages.php');
	}

	/**
	 * Returns the current language display name
	 */
	public function getDisplayLabel()
	{
		return \Locale::getDisplayLanguage($this->current, $this->current);
	}

	/**
	 * Returns translated string
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, $params = array())
	{
		$string = $this->get($key);
		return vsprintf($string, $params);
	}

	/**
	 * Save item
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value)
	{
		$this->storage[$key] = $value;
	}

	/**
	 * Return item
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return isset($this->storage[$key]) ? $this->storage[$key] : NULL;
	}

	/**
	 * Reset a session
	 * @return boolean
	 */
	public function reset()
	{
		$this->storage = array();
		return true;
	}
}