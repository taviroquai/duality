<?php

namespace Duality\System\Structure;

class DbTable extends Table
{

	protected $database;
	
	public function __construct(Database $database)
	{
		parent::__construct();
		$this->database = $database;
	}

	public function setPropertiesFromEntity(Entity $entity)
	{
		foreach ($entity->getProperties() as $property) {
			if (!$this->propertyExists($property)) {
				$this->addProperty($property);
			}
		}
	}

	public function loadFromEntity(Entity $entity, $offset = 0, $limit = false)
	{
		$this->setPropertiesFromEntity($entity);

		$sql = $this->database->getSelect('*', $entity, $offset, $limit);
		$stm = $this->database->getPDO()->query($sql);
		
		while ($trow = $stm->fetch(\PDO::FETCH_ASSOC)) {
			$row = new Row;
			$row->setTable($this);
			foreach ($entity->getProperties() as $property) {
				$data = new \Duality\System\Core\Data;
				$data->setValue($trow[(string) $property]);
				$row->addData($property, $data);
			}
			$this->addRow($row);
		}
	}
}