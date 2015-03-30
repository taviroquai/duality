<?php

/**
 * Interface for Validation
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.3
 */

namespace Duality\Core;

use Duality\Structure\Http\Response;

/**
 * Validation interface
 * 
 * Provides an interface for all Duality rule validators
 * ie. \Duality\Service\Validator
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   1.0.3
 */
interface InterfaceValidation
{
    /**
     * Validates authorization
     * 
     * @return boolean The authorization result
     */
    public function isValid();

}