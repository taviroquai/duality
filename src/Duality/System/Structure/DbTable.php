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
		$this->database = $database;
	}

    /**
     * Sets table properties from an entity (ORM functionality)
     * @param \Duality\System\Structure\Entity $entity
     */
	public function setPropertiesFromEntity(Entity $entity)
	{
		$this->setName((string) $entity);
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
		$this->loadPage($offset, $limit);
	}

	/**
     * Loads table values with limit
     * @param int $offset
     * @param int $limit
     */
	public function loadPage($offset = 0, $limit = false)
	{
		$sql = $this->database->getSelect('*', $this->getName(), $offset, $limit);
		$stm = $this->database->getPDO()->query($sql);
		
		while ($trow = $stm->fetch(\PDO::FETCH_ASSOC)) {
			$row = new TableRow;
			$row->setTable($this);
			foreach ($this->getProperties() as $property) {
				$data = new \Duality\System\Core\Data;
				$data->setValue($trow[(string) $property]);
				$row->addData($property, $data);
			}
			$this->addRow($row);
		}
	}
}