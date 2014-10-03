<?php

/**
 * Table row structure
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
use \Duality\Core\Data;

/**
 * Table row class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class TableRow 
extends Structure
{
    /**
     * The dependant table
     * 
     * @var \Duality\Structure\Table The table which belongs
     */
    protected $table;
    
    /**
     * Holds the table data
     * 
     * @var array Holds the row data
     */
    protected $data;

    /**
     * Creates a new table row
     */
    public function __construct()
    {
        $this->data = array();
    }

    /**
     * Sets the dependent table
     * 
     * @param \Duality\Structure\Table $table The table which belongs
     * 
     * @return void
     */
    public function setTable(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Gets the dependant table
     * 
     * @throws \Duality\Core\DualityException When row is orphan
     * 
     * @return \Duality\Structure\Table $table The table which belongs
     */
    public function getTable()
    {
        if (!is_subclass_of($this->table, 'Duality\Structure\Table')) {
            throw new DualityException("Row is orphan", 3);
        }
        return $this->table;
    }

    /**
     * Adds data to the row
     * 
     * @param \Duality\Structure\Property $property The property
     * @param string|int                  $data     The data to be added
     * 
     * @throws \Duality\Core\DualityException
     * 
     * @return void
     */
    public function addData(Property $property, $data)
    {
        if (!$this->getTable()->propertyExists($property)) {
            throw new DualityException(
                "Row property does not exists: " . $property, 1
            );
        }
        $this->data[(string) $property] = $data;
    }

    /**
     * Gets the row property data
     * 
     * @param \Duality\Structure\Property $property Give the property to identify
     * 
     * @throws \Duality\Core\DualityException When property does not exists
     * 
     * @return string|int The result data
     */
    public function getData(Property $property)
    {
        if (!$this->getTable()->propertyExists($property)) {
            throw new DualityException(
                "Row property does not exists: " . $property, 2
            );
        }
        return $this->data[(string) $property];
    }
}