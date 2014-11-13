<?php

/**
 * Table row structure
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
use Duality\Structure\Storage;
use Duality\Structure\Table;

/**
 * Table row class
 * 
 * PHP Version 5.3.4
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
     * @var \Duality\Structure\Storage Holds the row data
     */
    protected $data;

    /**
     * Creates a new table row
     * 
     * @param \Duality\Structure\Table $table The row table
     */
    public function __construct(Table $table)
    {
        $this->data = new Storage;
        $this->setTable($table);
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
     * @return \Duality\Structure\Table $table The table which belongs
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Adds data to the row
     * 
     * @param \Duality\Structure\Property $property The property
     * @param string|int                  $data     The data to be added
     * 
     * @return void
     */
    public function addData(Property $property, $data)
    {
        if ($this->getTable()->columnExists($property)) {
            $this->data->set((string) $property, $data);
        }
    }

    /**
     * Gets the row property data
     * 
     * @param \Duality\Structure\Property $property Give the property to identify
     * 
     * @return string|int The result data
     */
    public function getData(Property $property)
    {
        $result = null;
        if ($this->getTable()->columnExists($property)) {
            $result = $this->data->get((string) $property);
        }
        return $result;
    }
}