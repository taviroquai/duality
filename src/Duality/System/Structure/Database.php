<?php

namespace Duality\System\Structure;

use Duality\System\Core\Structure;
use Duality\System\Structure\Entity;

/**
 * Database class
 */
abstract class Database extends Structure {

    /**
     * Holds the PDO handler
     * @var \PDO
     */
	protected $pdo;

    /**
     * Holds the database tables structures
     * @var array
     */
	protected $tables = array();

    /**
     * Creates a new database connection
     * @param string $dsn
     * @param string $user
     * @param string $pass
     * @param array $options
     */
	public function __construct($dsn, $user, $pass, $options = array())
	{
		$this->pdo = new \PDO($dsn, $user, $pass, $options);
	}

    /**
     * Adds a table to the database
     * @param \Duality\System\Structure\Table $table
     */
	public function addTable(Table $table)
	{
		$this->tables[] = $table;
	}

    /**
     * Gets all database tables
     * @return array
     */
	public function getTables()
	{
		return $this->tables;
	}

    /**
     * Gets the database connection
     * @return \PDO
     */
	public function getPDO()
	{
		return $this->pdo;
	}

    public function createTableFromEntity(Entity $entity)
    {
        // Get a database table and its data from an entity
        $table = new DbTable($this);
        $table->setPropertiesFromEntity($entity);
        return $table;
    }

}