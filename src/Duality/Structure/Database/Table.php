<?php

/**
 * Database table structure
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.11.1
 */

namespace Duality\Structure\Database;

use Duality\Core\InterfaceStorage;
use Duality\Structure\TableRow;
use Duality\Structure\Table as DataTable;
use Duality\Structure\Entity;
use Duality\Service\Database;

/**
 * Database table class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.11.1
 */
class Table 
extends DataTable
{
    /**
     * Holds the database service instance
     * 
     * @var \Duality\Service\Database The database service
     */
    protected $database;

    /**
     * Holds the primary key column
     * 
     * @var string The primary key column
     */
    protected $primaryKey = 'id';
    
    /**
     * Creates a new database table giving a database structure
     * 
     * @param \Duality\Service\Database $database The database service
     */
    public function __construct(Database $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    /**
     * Sets the table primary key
     * 
     * @param string $name The primary key column name
     * 
     * @return void
     */
    public function setPrimaryKey($name)
    {
        $this->primaryKey = $name;
    }

    /**
     * Sets table properties from an array
     * 
     * @param array $columns Import columns from an associative array
     * 
     * @return void
     */
    public function setColumns($columns)
    {
        $this->columns->reset();
        foreach ($columns as $name => $item) {
            $property = new \Duality\Structure\Property($name);
            $this->addColumn($property);
        }
    }

    /**
     * Sets table properties from an entity (ORM functionality)
     * 
     * @param \Duality\Structure\Entity $entity Import columns from entity properties
     * 
     * @return void
     */
    public function setColumnsFromEntity(Entity $entity)
    {
        $this->setName((string) $entity);
        $this->columns->reset();
        foreach ($entity->getProperties() as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * Loads table values with limit
     * 
     * @param int    $offset The items offset (OFFSET clause)
     * @param int    $limit  The number of items (LIMIT clause)
     * @param string $where  The condition (WHERE clause)
     * @param array  $values The condition values as array
     * @param string $select The columns string (SELECT clause)
     * 
     * @return Table The resulting table
     */
    public function find(
        $offset = 0, $limit = 10, $where = '', $values = array(), $select = '*'
    ) {
        $sql = $this->database->getSelect(
            '*', $this->getName(), $where, $offset, $limit
        );
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute($values);
        
        $this->rows->reset();
        while ($trow = $stm->fetch(\PDO::FETCH_ASSOC)) {
            $row = new TableRow;
            $row->setTable($this);
            foreach ($this->getColumns() as $column) {
                $row->addData($column, $trow[(string) $column]);
            }
            $this->addRow($row);
        }
        return $this;
    }

    /**
     * Add item
     * 
     * @param string $key   Give the key to be identified
     * @param array  $value Give the item to be added
     * 
     * @return void
     */
    public function add($key, $value)
    {
        $sql = $this->database->getInsert($this, (array) $value);
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute($values);
    }

    /**
     * Update item
     * 
     * @param string $key   Give the key to be identified
     * @param array  $value Give the value to be updated
     * 
     * @return void
     */
    public function set($key, $value)
    {
        $sql = $this->database->getUpdate($this, (array) $value);
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute($values);
    }

    /**
     * Return item
     * 
     * @param string $key Give the value key
     * 
     * @return mixed The stored value
     */
    public function get($key)
    {
        $sql = $this->database->getSelect(
            '*', $this->getName(), 'id = ?', 0, 1
        );
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute(array($key));
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Checks if item exists
     * 
     * @param string $key Give the value key
     * 
     * @return boolean If exists, return true, false otherwise
     */
    public function has($key)
    {
        $sql = $this->database->getSelect(
            'count(*)', $this->getName(), 'id = ?', 0, 1
        );
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute(array($key));
        return (boolean) $stm->rowCount();
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        $sql = $this->database->getSelect('*', $this->getName(), 'id = ?');
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute(array($key));
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Loads items into storage
     * 
     * @param array $data The data to be loaded
     * 
     * @return void
     */
    public function importArray($data)
    {
        foreach ($data as $key => $item) {
            if ($this->has($key)) {
                $sql = $this->database->getInsert($this, $item);
            } else {
                $sql = $this->database->getUpdate($this, $item);
            }
            $stm = $this->database->getPDO()->prepare($sql);
            $stm->execute((array) $key);    
        }
    }

    /**
     * Remove item by its key
     * 
     * @param string $key Give the value key
     * 
     * @return void
     */
    public function remove($key)
    {
        $sql = $this->database->getDelete($this->getName());
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute(array($key));
    }

    /**
     * Clear storage
     * 
     * @return void
     */
    public function reset()
    {
        $sql = $this->database->getTruncate($this->getName());
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute();
    }
}