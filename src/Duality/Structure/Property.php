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

use Duality\Core\Structure;

/**
 * Property class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Property 
extends Structure
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