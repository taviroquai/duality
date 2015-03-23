<?php

/**
 * Interface Routable
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Core;

use Duality\Structure\Http\Request;

/**
 * Authorization Routable
 * 
 * Provides an interface for all Duality rule validators
 * ie. \Duality\Service\Validator
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
interface InterfaceRequestable
{
    /**
     * Pass the route params
     * 
     * @param \Duality\Structure\HTTP\Request The HTTP request
     */
    public function onRequest(Request $req);

}