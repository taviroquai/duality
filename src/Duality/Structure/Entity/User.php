<?php

/**
 * User structure
 * 
 * Provides a structure for an application user
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\Entity;

use Duality\Structure\Entity;

/**
 * Entity class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class User
extends Entity
{
    /**
     * Holds the entity configuration for extended functionality
     * 
     * @var array The list of configuration items
     */
    protected $config = array(
        'name' => 'users',
        'properties' => array('id', 'email', 'password')
    );
}