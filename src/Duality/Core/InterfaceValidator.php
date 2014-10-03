<?php

/**
 * Interface for data validator
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

/**
 * Validator interface
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceValidator
{
    /**
     * Validate rule name (key) with params
     *
     * @param string $key    Give the validation key as identifier
     * @param array  $params Give validation parameters as associative array
     * 
     * @return boolean
     */
    public function validate($key, $params);

    /**
     * Validate all rules - Each rule as a key
     * 
     * @param array $rules Give the validation rules - list of rules
     * 
     * @return boolean
     */
    public function validateAll($rules);

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