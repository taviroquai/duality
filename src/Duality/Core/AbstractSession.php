<?php

/**
 * Abstract session service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Core;

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
 * @since   2.0.0-dev
 */
abstract class AbstractSession
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