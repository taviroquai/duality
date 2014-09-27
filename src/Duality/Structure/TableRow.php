<?php

/**
 * Table row structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Structure;

use Duality\Core\Structure;
use Duality\Core\Data;

/**
 * Table row class
 */
class TableRow 
extends Structure
{	
    /**
     * The dependant table
     * @var \Duality\Structure\Table
     */
	protected $table;
    
    /**
     * Holds the table data
     * @var array
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
     * @param \Duality\Structure\Table $table
     */
	public function setTable(Table $table)
	{
		$this->table = $table;
	}

    /**
     * Gets the dependant table
     * @return \Duality\Structure\Table $table
     * @throws \Duality\Core\DualityException
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
     * @param \Duality\Structure\Property $property
     * @param \Duality\Core\Data $data
     * @throws \Duality\Core\DualityException
     */
	public function addData(Property $property, Data $data)
	{
		if (!$this->getTable()->propertyExists($property)) {
			throw new DualityException("Row property does not exists: " . $property, 1);
		}
		$this->data[(string) $property] = $data;
	}

    /**
     * Gets the row property data
     * @param \Duality\Structure\Property $property
     * @return string|int
     * @throws \Duality\Core\DualityException
     */
	public function getData(Property $property)
	{
		if (!$this->getTable()->propertyExists($property)) {
			throw new DualityException("Row property does not exists: " . $property, 2);
		}
		return $this->data[(string) $property];
	}

}