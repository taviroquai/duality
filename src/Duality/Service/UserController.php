<?php

/**
 * Controller service
 *
 * @since       0.9.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Service;

use Duality\Core\InterfaceService;
use Duality\App;

/**
 * Abstract user controller service
 */
class UserController
implements InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\App
	 */
	protected $app;

	/**
	 * Creates a new error handler
	 * @param Duality\App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Initiates the service
	 */
	public function init()
	{
        
	}

	/**
	 * Terminates the service
	 */
	public function terminate()
	{

	}
    
    
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
	 */
	public function __toString()
	{
		return class_name($this);
	}

}