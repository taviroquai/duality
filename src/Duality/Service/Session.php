<?php

/**
 * Abstract session service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.19.0
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceStorage;

/**
 * Abstract session service
 * 
 * Provides an interface for all Duality session handlers
 * ie. \Duality\Service\Session\Native
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.19.0
 */
abstract class Session
extends AbstractService
implements InterfaceStorage
{
    /**
     * Holds the session data
     * 
     * @var \Duality\Structure\Storage The session storage
     */
    protected $storage;

}