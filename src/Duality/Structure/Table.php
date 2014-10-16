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

use Duality\Core\Structure;
use Duality\Structure\Storage;

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
class Table 
extends Structure
{
    /**
     * Holds the table properties
     * 
     * @var \Duality\Structure\Storage Holds the table columns
     */
    protected $columns;

    /**
     * Holds the table rows
     * 
     * @var \Duality\Structure\Storage Holds the table rows
     */
    protected $rows = array();

    /**
     * Creates a new table
     * Initializes empty columns and empty rows
     */
    public function __construct()
    {
        $this->columns = new Storage;
        $this->rows    = new Storage;
    }

    /**
     * Adds a column to the table
     * 
     * @param \Duality\Structure\Property $property The new column as property
     * 
     * @return void
     */
    public function addColumn(Property $property)
    {
        $this->columns->add((string) $property, $property);
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
            $position = count($this->rows->asArray());
        }
        $this->insertRow($row, $position);
    }

    /**
     * Inserts a row in a specified position
     * 
     * @param \Duality\Structure\TableRow $row      The row to add
     * @param int                         $position The index position
     * 
     * @return void
     */
    public function insertRow(TableRow $row, $position = null)
    {
        if (empty($position)) {
            $position = count($this->rows->asArray());
        }
        $this->rows->set($position, $row);
    }

    /**
     * Gets all table properties
     * 
     * @return array Returns all columns
     */
    public function getColumns()
    {
        return $this->columns->asArray();
    }

    /**
     * Gets all table rows
     * 
     * @return array Returns the table rows
     */
    public function getRows()
    {
        return $this->rows->asArray();
    }

    /**
     * Check whether a property exists or not
     * 
     * @param \Duality\Structure\Property $property Give property to identify
     * 
     * @return boolean The check result
     */
    public function columnExists(Property $property)
    {
        return $this->columns->has((string) $property);
    }

    /**
     * Exports the table to array
     * 
     * @return array The table as array
     */
    public function toArray()
    {
        $out = array();
        foreach ($this->rows->asArray() as $row) {
            $trow = array();
            foreach ($this->columns->asArray() as $column) {
                $trow[(string) $column] = (string) $row->getData($column);
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
        foreach ($this->columns->asArray() as $column) {
            $out .= (string) $column;
            $out .= ',';
        }
        $out = rtrim($out, ',');
        $out .= PHP_EOL;

        foreach ($this->rows->asArray() as $row) {
            foreach ($this->columns->asArray() as $column) {
                $out .= (string) $row->getData($column);
                $out .= ',';
            }
            $out = rtrim($out, ',');
            $out .= PHP_EOL;
        }
        return $out;
    }
}