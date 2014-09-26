<?php

/**
 * Controller service
 *
 * @since       0.9.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceAuth;
use Duality\System\App;

/**
 * Abstract user controller service
 */
abstract class UserController
implements InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Creates a new error handler
	 * @param Duality\System\App $app
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

	/**
	 * Returns this class name
	 */
	public function __toString()
	{
		return class_name($this);
	}

}