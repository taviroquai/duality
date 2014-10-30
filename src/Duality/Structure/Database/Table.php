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
use Duality\Structure\Property;
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
     * Gets all table properties
     * 
     * @param boolean $cache The cached information
     * 
     * @return array Returns all columns
     */
    public function getColumns($cache = true)
    {
        if (!$cache) {
            $this->columns->reset();
            $result = $this->database->getPDO()->query(
                $this->database->getColumns($this)
            );
            while($item = $result->fetch(\PDO::FETCH_ASSOC)) {
                $property = new Property($item['name']);
                $this->addColumn($property);
            }
        }
        return parent::getColumns();
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
            $property = new Property($name);
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
     * @return \Duality\Structure\Database\Table This table
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
            $row = new TableRow($this);
            $row->setTable($this);
            foreach ($this->getColumns() as $column) {
                $row->addData($column, $trow[(string) $column]);
            }
            $this->addRow($row);
        }
        return $this;
    }

    /**
     * Loads table values with limit
     * 
     * @param \Duality\Structure\Database\Filter $filter The filter object
     * 
     * @return \Duality\Structure\Database\Table This table
     * 
     * @since 0.18.0
     */
    public function filter(Filter $filter) {
        $sql = $this->database->getSelect(
            $filter->getSelect(),
            $this->getName(),
            $filter->getWhere(),
            $filter->getGroupBy(),
            $filter->getOffset(),
            $filter->getLimit()
        );
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute($filter->getWhereValues());
        
        $this->rows->reset();
        $columns = explode(',', $filter->getSelect());
        while ($trow = $stm->fetch(\PDO::FETCH_ASSOC)) {
            $row = new TableRow($this);
            $row->setTable($this);
            foreach ($columns as $column) {
                $row->addData(
                    new Property($column),
                    $trow[(string) $column]
                );
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
        $stm->execute(array_values((array)$value));
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
        $stm->execute(array_values((array)$value));
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
        return $stm->fetch(\PDO::FETCH_ASSOC);
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
            'count(' . $this->primaryKey . ') as count', 
            $this->getName(), $this->primaryKey . ' = ?', 0, 1
        );
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute(array($key));
        $result = (int) $stm->fetchColumn(0);
        return (boolean) $result;
    }

    /**
     * Returns all items as array
     * 
     * @return array Returns all stored values
     */
    public function asArray()
    {
        $sql = $this->database->getSelect('*', $this->getName());
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
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
            if (!$this->has($key)) {
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
        $sql = $this->database->getDelete($this, array($this->primaryKey => $key));
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
        $sql = $this->database->getTruncate($this);
        $stm = $this->database->getPDO()->prepare($sql);
        $stm->execute();
    }
}