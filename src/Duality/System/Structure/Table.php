<?php

namespace Duality\System\Structure;

/**
 * Table class
 */
class Table extends \Duality\System\Core\Structure
{
    /**
     * Holds the table properties
     * @var array
     */
	protected $properties = array();

    /**
     * Holds the table rows
     * @var array
     */
	protected $rows = array();
	
    /**
     * Creates a new empty table
     */
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Adds a property to the table
     * @param \Duality\System\Structure\Property $property
     */
	public function addProperty(Property $property)
	{
		$this->properties[] = $property;
	}

    /**
     * Adds a row to the table
     * @param \Duality\System\Structure\Row $row
     * @param int $position
     */
	public function addRow(Row $row, $position = null)
	{
		if (empty($position)) {
			$position = count ($this->rows);
		}
		$this->rows[$position] = $row;
	}

    /**
     * Gets all table properties
     * @return array
     */
	public function getProperties()
	{
		return $this->properties;
	}

    /**
     * Gets all table rows
     * @return array
     */
	public function getRows()
	{
		return $this->rows;
	}

    /**
     * Check whether a property exists or not
     * @param \Duality\System\Structure\Property $property
     * @return boolean
     */
	public function propertyExists(Property $property)
	{
		return in_array($property, $this->getProperties());
	}

    /**
     * Exports the table to CSV
     * @return string
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