<?php

/**
 * Performance service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.15.1
 */

namespace Duality\Service;

use Duality\Structure\Storage;
use Duality\Core\AbstractService;

/**
 * Performance service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.15.1
 */
class Performance
extends AbstractService
{
    /**
     * Holds the performance data
     * 
     * @var \Duality\Structure\Storage The session storage
     */
    protected $storage;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        $this->storage = new Storage;
        $this->reset();
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {
        unset($this->storage);
    }

    /**
     * Add checkpoint
     * 
     * @param string $key Give the key which identifies the value
     * 
     * @return void
     */
    public function checkpoint($key)
    {
        $this->storage->add($key, microtime(true));
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        return $this->storage->asArray();
    }

    /**
     * Return current performance time
     * 
     * @return float Return current time as 0.000
     */
    public function getCurrentTime()
    {
        $start = $this->storage->get('START');
        return round(microtime(true) - $start, 3);
    }

    /**
     * Reset a session
     * 
     * @return void
     */
    public function reset()
    {
        $this->storage->reset();
        $this->checkpoint('START');
    }

}