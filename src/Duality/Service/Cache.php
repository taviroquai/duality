<?php

/**
 * Abstract cache service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\AbstractService;
use Duality\Core\InterfaceStorage;

/**
 * Abstract cache service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Cache
extends AbstractService
implements InterfaceStorage
{   
    /**
     * Put item
     * 
     * @param string $key    Give the key to identify the value
     * @param string $value  Give the value to be stored
     * @param int    $expire Give the time to live value as integer
     * 
     * @return void
     */
    abstract public function put($key, $value, $expire = 0);

    /**
     * Pull item
     * 
     * @param string $key Give the key to retrieve the value
     * 
     * @return mixed The value to be retrived or null
     */
    abstract public function pull($key);
    
}