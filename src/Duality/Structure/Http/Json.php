<?php

/**
 * HTTP json response
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */

namespace Duality\Structure\Http;

use Duality\Structure\Http\Response;

/**
 * HTTP json response
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   2.0.0-dev
 */
class Json
extends Response
{
    /**
     * Default response
     * 
     * @param \Duality\Structure\Http\Request $req Give the current request
     * 
     * @return void
     */
    public function onRequest(Request $req)
    {
        $this->headers->set('Content-Type', 'application/json');
    }
}