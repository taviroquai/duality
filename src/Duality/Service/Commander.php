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

use Duality\Core\AbstractService;
use Duality\Core\InterfaceCommander;

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
extends AbstractService
implements InterfaceCommander
{
    /**
     * Holds the input arguments
     * 
     * @var string Holds the user input command
     */
    protected $argv;

    /**
     * Commander responders
     * 
     * @var array Holds the list of callbacks responders
     */
    protected $responders;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        // Set input from globals
        $this->setInput(implode(' ', self::parseFromGlobals()));

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
        $this->argv = array();
    }

    /**
     * Sets the input arguments
     * 
     * @param string $argv Give the input line
     * 
     * @return void
     */
    public function setInput($argv)
    {
        $argv = explode(' ', $argv);
        $this->argv = array_slice($argv, 1);
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
            foreach ($this->argv as $item) {
                if ($result = preg_match($ns, $item, $matches)) {
                    $notfound = false;
                    $cb($matches);
                }
            }
        }
        if ($notfound) {
            $this->app->getBuffer()->write('Command not found'.PHP_EOL);
        }
    }

}