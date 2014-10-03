<?php

/**
 * Controller service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\InterfaceService;
use Duality\App;

/**
 * Abstract user controller service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class DefaultController
implements InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var \Duality\App Holds the application container
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
     * @param \Duality\Http\Request  &$req   Give the current request
     * @param \Duality\Http\Response &$res   Give the current response
     * @param array                  $params Give the URI params
     * 
     * @return void
     */
    public function doIndex(
        \Duality\Http\Request &$req,
        \Duality\Http\Response &$res,
        $params = array()
    ) {
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
        return class_name($this);
    }

}