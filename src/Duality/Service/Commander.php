<?php

/**
 * Commander service for non-web tasks
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
use Duality\Core\InterfaceCommander;
use Duality\App;

/**
 * Commander service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class Commander 
implements InterfaceCommander, InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var Duality\App Holds the application container
     */
    protected $app;

    /**
     * Holds the input arguments
     * 
     * @var string Holds the user input command
     */
    protected $argsv;

    /**
     * Commander responders
     * 
     * @var array Holds the list of callbacks responders
     */
    protected $responders;

    /**
     * Creates a new error handler
     * 
     * @param \Duality\App &$app The application container
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
        $argv = self::parseFromGlobals();
        $this->argsv = array_slice($argv, 1);

        // Register built-in responders
        $app = $this->app;
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        $this->argsv = array();
    }

    /**
     * Adds command responder
     * 
     * @param string   $uriPattern Give the regex pattern to match against user input
     * @param \Closure $cb         Give the callback responder
     * 
     * @return void
     */
    public function addResponder($uriPattern, $cb)
    {
        $this->responders[$uriPattern] = $cb;
    }

    /**
     * Parses the command input
     * 
     * @return string The user input
     */
    public static function parseFromGlobals()
    {
        return $_SERVER['argv'];
    }

    /**
     * Executes commander responders
     * 
     * @return void
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