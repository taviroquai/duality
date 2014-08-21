<?php

namespace Duality\System\Structure;

class Table extends \Duality\System\Core\Structure
{

	protected $properties = array();

	protected $rows = array();
	
	public function __construct()
	{
		parent::__construct();
	}

	public function addProperty(Property $property)
	{
		$this->properties[] = $property;
	}

	public function addRow(Row $row, $position = null)
	{
		if (empty($position)) {
			$position = count ($this->rows);
		}
		$this->rows[$position] = $row;
	}

	public function getProperties()
	{
		return $this->properties;
	}

	public function getRows()
	{
		return $this->rows;
	}

	public function propertyExists(Property $property)
	{
		return in_array($property, $this->getProperties());
	}

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