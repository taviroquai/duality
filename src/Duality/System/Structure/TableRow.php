<?php

/**
 * Table row structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\System\Structure;

use Duality\System\Core\Structure;
use Duality\System\Core\Data;

/**
 * Table row class
 */
class TableRow 
extends Structure
{	
    /**
     * The dependant table
     * @var \Duality\System\Structure\Table
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
     * @param \Duality\System\Structure\Table $table
     */
	public function setTable(Table $table)
	{
		$this->table = $table;
	}

    /**
     * Gets the dependant table
     * @return \Duality\System\Structure\Table $table
     * @throws \Duality\System\Core\DualityException
     */
	public function getTable()
	{
		if (!is_subclass_of($this->table, 'Duality\System\Structure\Table')) {
			throw new DualityException("Row is orphan", 3);
		}
		return $this->table;
	}

    /**
     * Adds data to the row
     * @param \Duality\System\Structure\Property $property
     * @param \Duality\System\Core\Data $data
     * @throws \Duality\System\Core\DualityException
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
     * @param \Duality\System\Structure\Property $property
     * @return string|int
     * @throws \Duality\System\Core\DualityException
     */
	public function getData(Property $property)
	{
		if (!$this->getTable()->propertyExists($property)) {
			throw new DualityException("Row property does not exists: " . $property, 2);
		}
		return $this->data[(string) $property];
	}

}