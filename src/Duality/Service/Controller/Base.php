<?php

/**
 * Controller service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service\Controller;

use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;
use Duality\Core\AbstractService;

/**
 * Default controller service
 * 
 * Provides a default controller for HTTP server
 * Used by \Duality\Core\InterfaceServer
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Base
extends AbstractService
{
    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {

    }
    
    /**
     * Default application action
     * 
     * @param \Duality\Structure\Http\Request  $req    Give the current request
     * @param \Duality\Structure\Http\Response $res    Give the current response
     * @param array                            $params Give the URI params
     * 
     * @return void
     */
    public function doIndex(Request &$req, Response &$res, $params = array())
    {
        $html = <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Duality default controller - Replace me!</title>
    </head>
    <body><h1>Duality default controller - Replace me!</h1></body>
</html>
EOF;
        $res->setContent($html);
        $res->setStatus(404);
    }

    /**
     * Returns this class name
     * 
     * @return string This class name
     */
    public function __toString()
    {
        return get_class($this);
    }

}