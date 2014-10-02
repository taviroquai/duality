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
	 * Holds the number formatter
	 * @var \NumberFormatter
	 */
	protected $numberFormatter;

	/**
	 * Holds the date/time formatter
	 * @var \IntlDateFormatter
	 */
	protected $datetimeFormatter;

	/**
	 * Holds the calendar
	 * @var \IntlCalendar
	 */
	protected $calendar;

	/**
	 * Holds the time zone
	 * @var \IntlTimeZone
	 */
	protected $timezone;

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
		if ($this->app->getConfigItem('locale.default') == NULL) {
			throw new DualityException("Error: locale configuration missing", 2);
		}
		if ($this->app->getConfigItem('locale.dir')) {
			$this->directory = $this->app->getConfigItem('locale.dir');
		}
		if (!is_dir($this->directory) || !is_readable($this->directory)) {
			throw new DualityException("Error: directory not readable: " . $this->directory, 3);
		}
		$request = $this->app->call('server')->getRequest();
		if (is_null($this->current)) {
			$this->current = $this->app->getConfigItem('locale.default');
		}
		$timezone = NULL;
		if ($this->app->getConfigItem('locale.timezone')) {
			$timezone = $this->app->getConfigItem('locale.timezone');
		}
		$this->setLocale($this->current, $timezone);
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
	 * @param string $timezone
	 */
	public function setLocale($code, $timezone = 'Europe/Lisbon')
	{
		$code = \Locale::canonicalize($code);
		$this->current = $code;

		// Validate locale and translations directory
		if (
			\Locale::acceptFromHttp($code) === NULL
			|| !is_dir($this->directory.DIRECTORY_SEPARATOR.$this->current)
		) {
			$this->current = \Locale::canonicalize(
				$this->app->getConfigItem('locale.default')
			);
		}

		// Define default locale
		\Locale::setDefault($this->current);
		$directory = $this->directory.DIRECTORY_SEPARATOR.$this->current;
		$this->storage = include($directory.DIRECTORY_SEPARATOR.'messages.php');

		// Create a number formater
		$this->numberFormatter = \NumberFormatter::create(
			$this->current, \NumberFormatter::DECIMAL
		);

		// Create a time zone
		$this->timezone = \IntlTimeZone::createTimeZone($timezone);

		// Create a calendar
		$this->calendar = \IntlCalendar::createInstance(
			$this->timezone, $this->current
		);

		// Create a DateTimeFormater
		$this->datetimeFormatter = new \IntlDateFormatter( 
			$this->current,
			\IntlDateFormatter::FULL,
			\IntlDateFormatter::FULL,
    		$timezone,
    		\IntlDateFormatter::GREGORIAN
    	);
	}

	/**
	 * Returns the current language display name
	 * @return string
	 */
	public function getDisplayLanguage()
	{
		return \Locale::getDisplayLanguage($this->current, $this->current);
	}

	/**
	 * Returns a formated number
	 * @param int $value
	 * @param int $style
	 * @param string
	 * @return string
	 */
	public function getNumber(
		$value,
		$style = \NumberFormatter::DECIMAL,
		$pattern = NULL
	) {
		$this->numberFormatter = \NumberFormatter::create(
			$this->current, $style, $pattern
		);
		return $this->getNumberFormatter()->format($value);
	}

	/**
	 * Returns a real number
	 * @param string $value
	 * @param int $type
	 * @return int
	 */
	public function parseNumber(
		$value,
		$type = \NumberFormatter::TYPE_DOUBLE
	) {
		return $this->getNumberFormatter()->parse($value, $type);
	}

	/**
	 * Returns a formated currency
	 * @param float $value
	 * @return string
	 */
	public function getCurrency($value, $currency = 'EUR') {
		$this->numberFormatter = \NumberFormatter::create(
			$this->current, \NumberFormatter::CURRENCY
		);
		return $this->getNumberFormatter()->formatCurrency($value, $currency);
	}

	/**
	 * Returns the number formatter
	 * @return \NumberFormatter
	 */
	public function getNumberFormatter()
	{
		return $this->numberFormatter;
	}

	/**
	 * Returns the date/time formatter
	 * @return \IntlDateFormatter
	 */
	public function getDateFormatter()
	{
		return $this->datetimeFormatter;
	}

	/**
	 * Returns the calendar
	 * @return \IntlCalendar
	 */
	public function getCalendar()
	{
		return $this->calendar;
	}

	/**
	 * Returns the time zone
	 * @return \IntlTimeZone
	 */
	public function getTimeZone()
	{
		return $this->timezone;
	}

	/**
	 * Returns translated string
	 * @param string $key
	 * @param array $params
	 * @return string
	 */
	public function translate($key, $params = array(), $target = NULL)
	{
		// Load defauts
		$current = $this->current;
		$directory = $this->directory.DIRECTORY_SEPARATOR.$current;
		$params = (array) $params;

		// Validate and load different $target
		if (!empty($target) && $target != $current) {
			$current = $target;
			$directory = $this->directory.DIRECTORY_SEPARATOR.$current;

			// Validate locale and translations directory
			if (
				\Locale::canonicalize($current) === NULL
				|| !is_dir($this->directory.DIRECTORY_SEPARATOR.$current)
			) {
				throw new Exception("Error Locale: target code ", 2);
			}
		}

		// Finally, return result
		$storage = include($directory.DIRECTORY_SEPARATOR.'messages.php');
		return \MessageFormatter::formatMessage(
			$current, $storage[$key], $params
		);
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