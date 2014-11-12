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

namespace Duality\Structure;

use Duality\Core\Structure;
use Duality\Structure\Property;
use Duality\Structure\TableRow;
use Duality\Structure\Storage;

/**
 * Table class
 * 
 * PHP Version 5.3.4
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
     * Remove column
     * 
     * @param string $name The name of the column to be removed
     * 
     * @since 0.14.2
     * 
     * @return \Duality\Structure\Table This table
     */
    public function removeColumn($name)
    {
        $this->columns->remove($name);
        return $this;
    }

    /**
     * Adds a row to the table
     * 
     * @param \Duality\Structure\TableRow $row The row to add
     * 
     * @return void
     */
    public function addRow(TableRow $row)
    {
        $this->insertRow($row);
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
     * @param boolean $cache The cached information
     * 
     * @return array Returns all columns
     */
    public function getColumns($cache = true)
    {
        return $this->columns->asArray();
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
     * Loads items into storage
     * 
     * @param array $data The data to be loaded
     * 
     * @return void
     */
    public function importArray($data)
    {
        foreach ($data as $key => $item) {
            $row = new TableRow($this);
            foreach ($this->getColumns() as $col => $prop) {
                if (isset($item[$col])) {
                    $property = $this->columns->get($col);
                    $row->addData($property, $item[$col]);
                }
                $this->insertRow($row, $key);
            }    
        }
    }

    /**
     * Resets table
     * 
     * @return void
     */
    public function reset()
    {
        $this->columns = new Storage;
        $this->rows    = new Storage;
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