<?php

/**
 * Abstract table structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

use Duality\Structure\Property;
use Duality\Structure\TableRow;

/**
 * Abstract Table class
 * 
 * Provides extended functionality for to deal with tables
 * ie. \Duality\Structure\Table
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceTable
{
    /**
     * Adds a column to the table
     * 
     * @param \Duality\Structure\Property $property The new column as property
     * 
     * @return void
     */
    public function addColumn(Property $property);

    /**
     * Remove column
     * 
     * @param string $name The name of the column to be removed
     * 
     * @since 0.14.2
     * 
     * @return \Duality\Structure\Table This table
     */
    public function removeColumn($name);

    /**
     * Adds a row to the table
     * 
     * @param \Duality\Structure\TableRow $row The row to add
     * 
     * @return void
     */
    public function addRow(TableRow $row);

    /**
     * Inserts a row in a specified position
     * 
     * @param \Duality\Structure\TableRow $row      The row to add
     * @param int                         $position The index position
     * 
     * @return void
     */
    public function insertRow(TableRow $row, $position = null);

    /**
     * Gets all table properties
     * 
     * @param boolean $cache The cached information
     * 
     * @return array Returns all columns
     */
    public function getColumns($cache = true);

    /**
     * Check whether a property exists or not
     * 
     * @param \Duality\Structure\Property $property Give property to identify
     * 
     * @return boolean The check result
     */
    public function columnExists(Property $property);

    /**
     * Loads items into storage
     * 
     * @param array $data The data to be loaded
     * 
     * @return void
     */
    public function importArray($data);

    /**
     * Resets table
     * 
     * @return void
     */
    public function reset();

    /**
     * Exports the table to array
     * 
     * @return array The table as array
     */
    public function toArray();

    /**
     * Exports the table to CSV
     * 
     * @return string The table as CVS format
     */
    public function toCSV();
    
}