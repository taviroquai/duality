<?php

/**
 * Localization service
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
use Duality\Structure\Storage;

/**
 * Default localization service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Localization 
extends AbstractService
{
    /**
     * Holds the directory for translations
     * 
     * @var string Holds the base directory for translations
     */
    protected $directory = './data/lang/';

    /**
     * Holds the messages storage
     * 
     * @var \Duality\Core\InterfaceStorage Holds the translation messages
     */
    protected $storage;

    /**
     * Holds the current iso localization parameters
     * 
     * @var string Holds the current locale string
     */
    protected $current;

    /**
     * Holds the number formatter
     * 
     * @var \NumberFormatter Holds the locale number formater
     */
    protected $numberFormatter;

    /**
     * Holds the date/time formatter
     * 
     * @var \IntlDateFormatter Holds the locale date formatter
     */
    protected $datetimeFormatter;

    /**
     * Holds the calendar
     * 
     * @var \IntlCalendar Holds the locale calendar
     */
    protected $calendar;

    /**
     * Holds the time zone
     * 
     * @var \IntlTimeZone Holds the current timezone
     */
    protected $timezone;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->storage = new Storage;
        $this->storage->reset();

        if ($this->app->getConfigItem('locale.default') == null) {
            throw new DualityException("Error: locale configuration missing", 2);
        }
        if ($this->app->getConfigItem('locale.dir')) {
            $this->directory = $this->app->getConfigItem('locale.dir');
        }
        if (!is_dir($this->directory) || !is_readable($this->directory)) {
            throw new DualityException(
                "Error: directory not readable: " . $this->directory, 3
            );
        }
        $timezone = null;
        if ($this->app->getConfigItem('locale.timezone')) {
            $timezone = $this->app->getConfigItem('locale.timezone');
        }
        $this->current = $this->app->getConfigItem('locale.default');
        $this->setLocale($this->current, $timezone);
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
     * Loads all locale settings
     * 
     * @param string $code     Give the locale code
     * @param string $timezone Give the timezone string
     * 
     * @return void
     */
    public function setLocale($code, $timezone = 'Europe/Lisbon')
    {
        $code = \Locale::canonicalize($code);
        $this->current = $code;

        // Validate locale and translations directory
        if (\Locale::acceptFromHttp($code) === null
            || !is_dir($this->directory.DIRECTORY_SEPARATOR.$this->current)
        ) {
            $this->current = \Locale::canonicalize(
                $this->app->getConfigItem('locale.default')
            );
        }

        // Define default locale
        \Locale::setDefault($this->current);
        $directory = $this->directory.DIRECTORY_SEPARATOR.$this->current;
        if (!file_exists($directory.DIRECTORY_SEPARATOR.'messages.php')) {
            throw new DualityException(
                "Error locale: invalid messages file ".$this->current, 3
            );
        }
        $this->storage->importArray(
            include($directory.DIRECTORY_SEPARATOR.'messages.php')
        );

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
     * 
     * @return string The locale language to display
     */
    public function getDisplayLanguage()
    {
        return \Locale::getDisplayLanguage($this->current, $this->current);
    }

    /**
     * Returns a formated number
     * 
     * @param int    $value   Give the number to format
     * @param int    $style   Give the constant \NumberFormatter style
     * @param string $pattern Give pattern if required by style parameter
     * 
     * @return string The resulting string
     */
    public function getNumber(
        $value,
        $style = \NumberFormatter::DECIMAL,
        $pattern = null
    ) {
        $this->numberFormatter = \NumberFormatter::create(
            $this->current, $style, $pattern
        );
        return $this->getNumberFormatter()->format($value);
    }

    /**
     * Returns a real number
     * 
     * @param string $value The string to parse
     * @param int    $type  The type of format as \NumberFormatter
     * 
     * @return int The resulting number
     */
    public function parseNumber(
        $value,
        $type = \NumberFormatter::TYPE_DOUBLE
    ) {
        return $this->getNumberFormatter()->parse($value, $type);
    }

    /**
     * Returns a formated currency
     * 
     * @param float  $value    The number to format
     * @param string $currency The target currency to format
     * 
     * @return string The resulting string
     */
    public function getCurrency($value, $currency = 'EUR')
    {
        $this->numberFormatter = \NumberFormatter::create(
            $this->current, \NumberFormatter::CURRENCY
        );
        return $this->getNumberFormatter()->formatCurrency($value, $currency);
    }

    /**
     * Returns the number formatter
     * 
     * @return \NumberFormatter The current number formatter instance
     */
    public function getNumberFormatter()
    {
        return $this->numberFormatter;
    }

    /**
     * Returns the date/time formatter
     * 
     * @return \IntlDateFormatter The current date formatter instance
     */
    public function getDateFormatter()
    {
        return $this->datetimeFormatter;
    }

    /**
     * Returns the calendar
     * 
     * @return \IntlCalendar The current calendar instance
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Returns the time zone
     * 
     * @return \IntlTimeZone The current timezone instance
     */
    public function getTimeZone()
    {
        return $this->timezone;
    }

    /**
     * Returns translated string
     * 
     * @param string $key    The key to translate
     * @param array  $params The string values to passe in
     * @param string $target The target locale string if diferent than current
     * 
     * @return string The resulting string
     */
    public function translate($key, $params = array(), $target = null)
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
            if (\Locale::canonicalize($current) === null
                || !is_dir($this->directory.DIRECTORY_SEPARATOR.$current)
            ) {
                throw new DualityException("Error Locale: target code ", 2);
            }
        }

        // Finally, return result
        $storage = new Storage;
        $storage->importArray(
            include($directory.DIRECTORY_SEPARATOR.'messages.php')
        );
        return \MessageFormatter::formatMessage(
            $current, $storage->get($key), $params
        );
    }

    /**
     * Translate alias
     * 
     * @param string $key    Give the message key
     * @param array  $params Give the message values
     * @param string $target Give the target locale
     * 
     * @return string The translated message
     */
    public function t($key, $params = array(), $target = null)
    {
        return $this->translate($key, $params, $target);
    }
}