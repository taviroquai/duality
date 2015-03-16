<?php

/**
 * Basic property structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\AbstractStructure;

/**
 * Property class
 * 
 * Provides extended functionality for entities and table properties
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Property 
extends AbstractStructure
{
    /**
     * Creates a new property
     * 
     * @param string $name Give a name to property
     */
    public function __construct($name = '')
    {
        if (!empty($name)) {
            $this->setName($name);
        }
    }
}