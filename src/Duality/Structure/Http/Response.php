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
     * The default content
     * 
     * @var string The default content
     */
    protected $content = 'Welcome to Duality';
    
    /**
     * Default response
     * 
     * @param \Duality\Structure\Http\Request $req Give the current request
     * 
     * @return void
     */
    public function onRequest(Request $req)
    {
        if ($req->isAjax()) {
            $this->setContent(json_encode(array('content' => $this->getContent())));
        }
    }
}