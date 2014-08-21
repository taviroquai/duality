<?php

namespace Duality\System\Structure;

use Duality\System\Core\Data;

class Row {
	
	protected $table;
	protected $data;

	public function __construct()
	{
		$this->data = array();
	}

	public function setTable(Table $table)
	{
		$this->table = $table;
	}

	public function getTable()
	{
		if (!is_subclass_of($this->table, 'Duality\System\Structure\Table')) {
			throw new \Exception("Row is orphan", 3);
		}
		return $this->table;
	}

	public function addData(Property $property, Data $data)
	{
		if (!$this->getTable()->propertyExists($property)) {
			throw new \Exception("Row property does not exists: " . $property, 1);
		}
		$this->data[(string) $property] = $data;
	}

	public function getData(Property $property)
	{
		if (!$this->getTable()->propertyExists($property)) {
			throw new \Exception("Row property does not exists: " . $property, 2);
		}
		return $this->data[(string) $property];
	}

}