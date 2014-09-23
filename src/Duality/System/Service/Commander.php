<?php

namespace Duality\System\Service;

use Duality\System\Core\InterfaceService;
use Duality\System\Core\InterfaceCommander;
use Duality\System\App;

/**
 * Commander service
 */
class Commander implements InterfaceCommander, InterfaceService
{
	/**
	 * The dependent application container
	 * @var Duality\System\App
	 */
	protected $app;

	/**
	 * Holds the input arguments
	 */
	protected $argsv;

	/**
     * Commander responders
     * @var array
     */
	protected $responders;

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
		$argv = self::parseFromGlobals();
		$this->argsv = array_slice($argv, 1);

		// Register default responders
		$app = $this->app;
		$this->addResponder('/^db:create$/i', function($matches) use ($app) {
			return $app->call('db')->createFromConfig($app->getConfig());
		});
		$this->addResponder('/^db:update$/i', function($matches) use ($app) {
			return $app->call('db')->updateFromConfig($app->getConfig());
		});
		$this->addResponder('/^db:seed$/i', function($matches) use ($app) {
			return $app->call('db')->seedFromConfig($app->getConfig());
		});
	}

	/**
	 * Terminate service
	 */
	public function terminate()
	{
		$this->$argv = array();
	}

	/**
     * Adds command responder
     * @param string $uriPattern
     * @param \Closure $cb
     */
	public function addResponder($uriPattern, $cb)
	{
		$this->responders[$uriPattern] = $cb;
	}

	/**
	 * Parses the command input
	 */
	public static function parseFromGlobals()
	{
		return $_SERVER['argv'];
	}

	/**
	 * Executes commander responders
	 */
	public function listen()
	{
		$notfound = true;
		foreach ($this->responders as $ns => $cb) {
			foreach ($this->argsv as $item) {
				if ($result = preg_match($ns, $item, $matches)) {
					$notfound = false;
					$cb($matches);
				}
			}
		}
		if ($notfound) {
			echo 'Command not found'.PHP_EOL;
		}
	}

}