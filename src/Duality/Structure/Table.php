<?php

/**
 * Abstract table structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use \Duality\Core\Structure;

/**
 * Table class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Table 
extends Structure
{
    /**
     * Holds the table properties
     * 
     * @var array Holds the table columns
     */
    protected $columns = array();

    /**
     * Holds the table rows
     * 
     * @var array Holds the table rows
     */
    protected $rows = array();

    /**
     * Adds a column to the table
     * 
     * @param \Duality\Structure\Property $property The new column as property
     * 
     * @return void
     */
    public function addColumn(Property $property)
    {
        $this->columns[(string) $property] = $property;
    }

    /**
     * Adds a row to the table
     * 
     * @param \Duality\Structure\TableRow $row      The row to add
     * @param int                         $position The index position
     * 
     * @return void
     */
    public function addRow(TableRow $row, $position = null)
    {
        if (empty($position)) {
            $position = count($this->rows);
        }
        $this->rows[$position] = $row;
    }

    /**
     * Gets all table properties
     * 
     * @return array Returns all columns
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Gets all table rows
     * 
     * @return array Returns the table rows
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Check whether a property exists or not
     * 
     * @param \Duality\Structure\Property $property Give property to identify
     * 
     * @return boolean The check result
     */
    public function propertyExists(Property $property)
    {
        return in_array($property, $this->getProperties());
    }

    /**
     * Exports the table to array
     * 
     * @return array The table as array
     */
    public function toArray()
    {
        $out = array();
        $properties = $this->getProperties();

        foreach ($this->getRows() as $row) {
            $trow = array();
            foreach ($properties as $property) {
                $trow[(string) $property] = (string) $row->getData($property);
            }
            $out[] = $trow;
        }
        return $out;
    }

    /**
     * Exports the table to CSV
     * 
     * @return string The table as CVS format
     */
    public function toCSV()
    {
        $out = "";
        $properties = $this->getProperties();
        foreach ($properties as $property) {
            $out .= (string) $property;
            $out .= ',';
        }
        $out = rtrim($out, ',');
        $out .= PHP_EOL;

        foreach ($this->getRows() as $row) {
            foreach ($properties as $property) {
                $out .= (string) $row->getData($property);
                $out .= ',';
            }
            $out = rtrim($out, ',');
            $out .= PHP_EOL;
        }
        return $out;
    }
}