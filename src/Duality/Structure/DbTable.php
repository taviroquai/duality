<?php

/**
 * Database table structure
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Structure;

use Duality\Service\Database;

/**
 * Database table class
 */
class DbTable 
extends Table
{
    /**
     * Holds the dependant database
     * @var \Duality\Structure\Database
     */
	protected $database;
	
    /**
     * Creates a new database table giving a database structure
     * @param \Duality\Structure\Database $database
     */
	public function __construct(Database $database)
	{
		$this->database = $database;
	}

    /**
     * Sets table properties from an entity (ORM functionality)
     * @param \Duality\Structure\Entity $entity
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
     * Loads table values with limit
     * @param int $offset
     * @param int $limit
     * @param string $where
     * @param array $values
     * @return DbTable
     */
	public function find($offset = 0, $limit = 10, $where = '', $values = array())
	{
		$sql = $this->database->getSelect('*', $this->getName(), $where, $offset, $limit);
		$stm = $this->database->getPDO()->prepare($sql);
		$stm->execute($values);
		
		$this->rows = array();
		while ($trow = $stm->fetch(\PDO::FETCH_ASSOC)) {
			$row = new TableRow;
			$row->setTable($this);
			foreach ($this->getProperties() as $property) {
				$data = new \Duality\Core\Data;
				$data->setValue($trow[(string) $property]);
				$row->addData($property, $data);
			}
			$this->addRow($row);
		}
		return $this;
	}
}