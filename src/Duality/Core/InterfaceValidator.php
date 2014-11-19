<?php

/**
 * Interface for data validator
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

use Duality\Structure\RuleItem;

/**
 * Validator interface
 * 
 * Provides an interface for all Duality rule validators
 * ie. \Duality\Service\Validator
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceValidator
{
    /**
     * Adds a rule to be validated
     * 
     * @param \Duality\Structure\RuleItem The item to be validated
     * 
     * @return void
     */
    public function addRuleItem(RuleItem $item);

    /**
     * Validates all the rules
     * 
     * @return boolean The validation result
     */
    public function validate();

    /**
     * Get all validation messages
     * 
     * @return array Retuns the validation messages
     */
    public function getMessages();

    /**
     * Get validation result
     * 
     * @return boolean Tells whether the validation is success
     */
    public function ok();

}