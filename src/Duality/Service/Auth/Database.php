<?php

/**
 * Database authentication service
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */

namespace Duality\Service\Auth;

use Duality\Core\DualityException;
use Duality\Structure\Database\Filter;
use Duality\Service\Auth;

/**
 * Default authentication service
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */
class Database
extends Auth
{
    /**
     * Holds the database auth configuration
     * 
     * var array The configuration params
     */
    protected $config;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('auth.table')
            || !$this->app->getConfigItem('auth.user')
            || !$this->app->getConfigItem('auth.pass')
        ) {
            throw new DualityException(
                "Error Config: auth configuration (table|user|pass) not found",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        $this->config = $this->app->getConfigItem('auth');
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
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username
     * @param string $password The authentication password
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password)
    {
        $tableName = $this->config['table'];
        $userField = $this->config['user'];
        $passField = $this->config['pass'];
        
        $table = $this->app->call('db')->getTable($tableName);
        $filter = new Filter($table);
        $filter->columns('email,pass')
            ->where(
                "$userField = ? and $passField = ?", 
                array($username, $password)
            )->limit(0, 1);
        $table->filter($filter);
        $result = $table->toArray();
        if (count($result)) {
            $this->app->call('session')
                ->set($this->sessionKey, $username);
            return true;    
        }
        return false;
    }
}