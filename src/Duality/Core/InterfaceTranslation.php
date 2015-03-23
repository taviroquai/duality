<?php

/**
 * Localization service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Core;

/**
 * Default localization service
 * 
 * Provides an interface for all Duality localization services
 * ie. \Duality\Service\Localization
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
interface InterfaceTranslation
{
    /**
     * Loads all locale settings
     * 
     * @param string $code     Give the locale code
     * @param string $timezone Give the timezone string
     * 
     * @return void
     */
    public function setLocale($code, $timezone = 'Europe/Lisbon');

    /**
     * Returns the current language display name
     * 
     * @return string The locale language to display
     */
    public function getDisplayLanguage();

    /**
     * Returns translated string
     * 
     * @param string $key    The key to translate
     * @param array  $params The string values to passe in
     * @param string $target The target locale string if diferent than current
     * 
     * @return string The resulting string
     */
    public function translate($key, $params = array(), $target = null);

}