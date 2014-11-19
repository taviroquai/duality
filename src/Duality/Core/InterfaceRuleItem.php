<?php

/**
 * Interface rule item
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
 * Interface rule item
 * 
 * Provides an interface for Duality rule item
 * Used by \Duality\Service\Validator
 * ie. \Duality\Structure\RuleItem
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.0-beta
 */
interface InterfaceRuleItem
{
    /**
     * Creates a new validation item
     * 
     * @param string $key     The item key
     * @param string $value   The value to validate
     * @param string $filters The list of filters separated by "|"
     */
    public function __construct($key, $value, $filters);

    /**
     * Sets the pass message
     * 
     * @param string $msg The pass message
     * 
     * @return void
     */
    public function setPassMessage($msg);

    /**
     * Sets the fail message
     * 
     * @param string $msg The fail message
     * 
     * @return void
     */
    public function setFailMessage($msg);

    /**
     * Sets the validation result
     * 
     * @param boolean $result The result as true or false
     * 
     * @return void
     */
    public function setResult($result);

    /**
     * Gets the rule key
     * 
     * @return string
     */
    public function getKey();

    /**
     * Gets the value to be validated
     * 
     * @return string
     */
    public function getValue();

    /**
     * Gets the filters
     * 
     * @return array
     */
    public function getFilters();

    /**
     * Gets the validation result
     * 
     * @return boolean
     */
    public function getResult();

    /**
     * Gets the validation message
     * 
     * @return void
     */
    public function getMessage();

}