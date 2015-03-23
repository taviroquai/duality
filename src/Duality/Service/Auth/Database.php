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
use Duality\Core\AbstractAuth;
use Duality\Structure\Storage;

/**
 * Database authentication service
 * 
 * Provides operations to authenticate against Duality database
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */
class Database
extends AbstractAuth
{
    /**
     * Holds the database auth configuration
     * 
     * var \Duality\Structure\Storage The configuration params
     */
    protected $config;

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('auth.db.table')
            || !$this->app->getConfigItem('auth.db.userfield')
            || !$this->app->getConfigItem('auth.db.passfield')
        ) {
            throw new DualityException(
                "Error Config: auth.db configuration (table, userfield or passfield) not found",
                DualityException::E_CONFIG_NOTFOUND
            );
        }
        $this->config = new Storage;
        $this->setConfig(
            $this->app->getConfigItem('auth.db.table'),
            $this->app->getConfigItem('auth.db.userfield'),
            $this->app->getConfigItem('auth.db.passfield')
        );
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
        $this->status = false;
        $tableName = $this->config->get('table');
        $userField = $this->config->get('userfield');
        $passField = $this->config->get('passfield');
        
        $table = $this->app->call('db')->getTable($tableName);
        $filter = new Filter($table);
        $filter->columns('*')
            ->where(
                "$userField = ? and $passField = ?", 
                array($username, $password)
            )->limit(0, 1);
        $table->filter($filter);
        $result = $table->toArray();
        if (count($result)) {
            $this->status = true;    
        }
        return $this->status;
    }
    
    /**
     * Sets the authentication configuration
     * 
     * @param string $tablename The table containing the authentication data
     * @param string $userField The field containing the username
     * @param string $passField The field containing the password
     */
    public function setConfig($tablename, $userField, $passField)
    {
        $this->config->set('table', $tablename);
        $this->config->set('userfield', $userField);
        $this->config->set('passfield', $passField);
    }
}