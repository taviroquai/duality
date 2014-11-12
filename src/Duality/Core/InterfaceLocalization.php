<?php

/**
 * Localization service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */

namespace Duality\Core;

/**
 * Default localization service
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */
interface InterfaceLocalization
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
    );

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
    );

    /**
     * Returns a formated currency
     * 
     * @param float  $value    The number to format
     * @param string $currency The target currency to format
     * 
     * @return string The resulting string
     */
    public function getCurrency($value, $currency = 'EUR');

    /**
     * Returns the number formatter
     * 
     * @return \NumberFormatter The current number formatter instance
     */
    public function getNumberFormatter();

    /**
     * Returns the date/time formatter
     * 
     * @return \IntlDateFormatter The current date formatter instance
     */
    public function getDateFormatter();

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