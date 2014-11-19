<?php

/**
 * Database table interface
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.11.1
 */

namespace Duality\Structure\Database;

use Duality\Service\Database;

/**
 * Database table interface
 * 
 * Provides an interface for database tables
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.11.1
 */
interface InterfaceDatabaseTable
{   
    /**
     * Creates a new database table giving a database structure
     * 
     * @param \Duality\Service\Database $database The database service
     */
    public function __construct(Database $database);

    /**
     * Sets the table primary key
     * 
     * @param string $name The primary key column name
     * 
     * @return void
     */
    public function setPrimaryKey($name);

    /**
     * Gets the table primary key
     * 
     * @return string The primary key field
     */
    public function getPrimaryKey();

    /**
     * Gets all table properties
     * 
     * @param boolean $cache The cached information
     * 
     * @return array Returns all columns
     */
    public function getColumns($cache = true);

    /**
     * Sets table properties from an array
     * 
     * @param array $columns Import columns from an associative array
     * 
     * @return void
     */
    public function setColumns($columns);

    /**
     * Loads table values with limit
     * 
     * @param \Duality\Structure\Database\Filter $filter The filter object
     * 
     * @return \Duality\Structure\Database\Table This table
     * 
     * @since 0.18.0
     */
    public function filter(Filter $filter);

    /**
     * Add item
     * 
     * @param string $key   Give the key to be identified
     * @param array  $value Give the item to be added
     * 
     * @return void
     */
    public function add($key, $value);

    /**
     * Update item
     * 
     * @param string $key   Give the key to be identified
     * @param array  $value Give the value to be updated
     * 
     * @return void
     */
    public function set($key, $value);

    /**
     * Return item
     * 
     * @param string $key Give the value key
     * 
     * @return mixed The stored value
     */
    public function get($key);

    /**
     * Checks if item exists
     * 
     * @param string $key Give the value key
     * 
     * @return boolean If exists, return true, false otherwise
     */
    public function has($key);

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function toArray();

    /**
     * Loads items into storage
     * 
     * @param array $data The data to be loaded
     * 
     * @return void
     */
    public function importArray($data);

    /**
     * Remove item by its key
     * 
     * @param string $key Give the value key
     * 
     * @return void
     */
    public function remove($key);

    /**
     * Clear storage
     * 
     * @return void
     */
    public function reset();
}