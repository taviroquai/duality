<?php

/**
 * HTTP response structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure\Http;

use Duality\Core\InterfaceRequestable;
use Duality\Structure\Http\Request;
use Duality\Structure\Http;

/**
 * HTTP response class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Response
extends Http
implements InterfaceRequestable
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
        $this->setContent(
'<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Duality default controller - Replace me!</title>
    </head>
    <body><h1>Duality default controller - Replace me!</h1></body>
</html>'
        );
        $this->setStatus(404);
    }
}