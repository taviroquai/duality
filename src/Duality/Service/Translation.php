<?php

/**
 * Translation service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Core\InterfaceTranslation;
use Duality\Structure\Storage;

/**
 * Default Translation service
 * 
 * Provides functionality for localization operations
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
class Translation 
extends AbstractService
implements InterfaceTranslation
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
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->storage = new Storage;
        $this->storage->reset();

        if ($this->app->getConfigItem('idiom.default') == null) {
            throw new DualityException(
                "Error: idiom configuration missing",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        $this->directory = $this->app->getPath()
                . DIRECTORY_SEPARATOR
                . $this->directory;
        if ($this->app->getConfigItem('idiom.dir')) {
            $this->directory = $this->app->getPath()
                . DIRECTORY_SEPARATOR
                . $this->app->getConfigItem('idiom.dir');
        }
        if (!is_dir($this->directory) || !is_readable($this->directory)) {
            throw new DualityException(
                "Error: idiom directory not readable: " . $this->directory,
                DualityException::E_FILE_NOTWRITABLE
            );
        }
        $timezone = 'Europe/Lisbon';
        if ($this->app->getConfigItem('idiom.timezone')) {
            $timezone = $this->app->getConfigItem('idiom.timezone');
        }
        
        $this->current = $this->app->getConfigItem('idiom.default');
        $this->setLocale($this->current, $timezone);
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        unset($this->current);
        unset($this->directory);
        unset($this->storage);
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
        $this->current = (string) $code;
        date_default_timezone_set($timezone);

        // Validate messages file
        $directory = $this->directory
            . DIRECTORY_SEPARATOR
            . $this->current;
        if (!file_exists($directory.DIRECTORY_SEPARATOR.'messages.php')) {
            throw new DualityException(
                "Error idiom: invalid messages file ".$this->current,
                DualityException::E_FILE_NOTFOUND
            );
        }

        // Load idiom messages
        $this->storage->importArray(
            include($directory.DIRECTORY_SEPARATOR.'messages.php')
        );
    }

    /**
     * Returns the current language display name
     * 
     * @return string The locale language to display
     */
    public function getDisplayLanguage()
    {
        return $this->current;
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
            if (!is_dir($this->directory.DIRECTORY_SEPARATOR.$current)) {
                throw new DualityException(
                    "Error idiom: directory not found ",
                    DualityException::E_LOCALE_NOTFOUND
                );
            }
        }

        // Finally, return result
        $storage = new Storage;
        $storage->importArray(
            include($directory.DIRECTORY_SEPARATOR.'messages.php')
        );
        return vsprintf($storage->get($key), $params);
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