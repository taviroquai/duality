<?php

namespace Duality\System\Structure;

/**
 * Database table class
 */
class DbTable extends Table
{
    /**
     * Holds the dependant database
     * @var \Duality\System\Structure\Database
     */
	protected $database;
	
    /**
     * Creates a new database table giving a database structure
     * @param \Duality\System\Structure\Database $database
     */
	public function __construct(Database $database)
	{
		parent::__construct();
		$this->database = $database;
	}

    /**
     * Sets table properties from an entity (ORM functionality)
     * @param \Duality\System\Structure\Entity $entity
     */
	public function setPropertiesFromEntity(Entity $entity)
	{
		foreach ($entity->getProperties() as $property) {
			if (!$this->propertyExists($property)) {
				$this->addProperty($property);
			}
		}
	}

    /**
     * Loads table values from a given entity
     * @param \Duality\System\Structure\Entity $entity
     * @param int $offset
     * @param int $limit
     */
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