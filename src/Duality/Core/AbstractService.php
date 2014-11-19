<?php

/**
 * Interface for generic service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

use Duality\Core\Structure;
use Duality\App;

/**
 * Abstract Service
 * 
 * Provides common attributes as methods for all Duality services
 * Used by all services at \Duality\Service\*
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class AbstractService
extends Structure
{
    /**
     * The dependent application container
     * 
     * @var Duality\App Holds the application container
     */
    protected $app;

    /**
     * Creates a new error handler
     * 
     * @param \Duality\App &$app Give the application container
     */
    public function __construct(App &$app)
    {
        $this->app = $app;
    }

    /**
     * Initiates the service
     * 
     * @return void
     */
    public abstract function init();

    /**
     * Terminates the service
     * 
     * @return void
     */
    public abstract function terminate();
}