<?php

/**
 * MySql query writer
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Database;

use Duality\Structure\Property;
use \Duality\Service\Database;
use \Duality\Structure\DbTable;
use \Duality\App;

/**
 * MySql database query writer
 */
class MySql extends Database
{
    /**
     * The dependent application container
     * @var Duality\App
     */
    protected $app;

    /**
     * Creates a new error handler
     * @param Duality\App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * Returns a select query
     * @param string $fields
     * @param string $from
     * @param string $where
     * @param string $limit
     * @param string $offset
     * @return string
     */
    public function getSelect($fields, $from, $where, $limit, $offset)
    {
        $sql = "SELECT $fields FROM ".strtolower((string) $from);
        if (!empty($where)) {
            $sql .= " WHERE ".$where;
        }
        if ($limit > 0) {
            $sql .= ' LIMIT '.$limit.' OFFSET '.$offset;
        }
        return $sql;
    }

    /**
     * Returns a create table statement
     * @param Duality\Structure\DbTable $table
     * @param array $config
     * @return string
     */
    public function getCreateTable(DbTable $table, $config = array())
    {
        $sql = "CREATE TABLE " . strtolower((string) $table) . " ( ";

        foreach ($config as $field => $definition) {
            if ($definition == 'auto') {
                $definition = 'INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY';
            }
            $sql .= $field . " " . $definition . ",";
        }
        $sql = rtrim($sql, ',');
        $sql .= ');';
        return $sql;
    }

    /**
     * Returns a drop table statement
     * @param Duality\Structure\DbTable $table
     * @param boolean $ifExists
     * @return string
     */
    public function getDropTable(DbTable $table, $ifExists = true)
    {
        $sql = "DROP TABLE ";
        if ($ifExists) {
            $sql .= "IF EXISTS ";
        }
        $sql .= strtolower((string) $table);
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns a add column statement
     * @param Duality\Structure\DbTable $table
     * @param string $property
     * @param string $definition
     * @return string
     */
    public function getAddColumn(DbTable $table, $property, $definition)
    {
        $sql  = "ALTER TABLE " . strtolower((string) $table) . " ";
        $sql .= "ADD COLUMN " . strtolower((string) $property) . " ";
        $sql .= $definition;
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns a modify column statement
     * @param Duality\Structure\DbTable $table
     * @param Duality\Structure\Property $property
     * @param string $definition
     * @return string
     */
    public function getModifyColumn(DbTable $table, Property $property, $definition)
    {
        $sql  = "ALTER TABLE " . strtolower((string) $table) . " ";
        $sql .= "MODIFY COLUMN " . strtolower((string) $property) . " ";
        $sql .= $definition;
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns a insert table statement
     * @param Duality\Structure\DbTable $table
     * @param array $item
     * @return string
     */
    public function getInsert(DbTable $table, $item = array())
    {
        $sql = "INSERT INTO " . strtolower((string) $table) . " ( ";

        $values = array();
        foreach ($item as $field => $value) {
            $values[] = $this->parseValue($value);
            $sql .= $field. ",";
        }
        $sql = rtrim($sql, ',');
        $sql .= ') ';

        if (!empty($values)) {
            $sql .= ' VALUES (';
            foreach ($values as $item) {
                $sql .= '?,';
            }
            $sql = rtrim($sql, ',');
            $sql .= ') ';
        }
        return $sql;
    }

    /**
     * Returns an update table statement
     * @param Duality\Structure\DbTable $table
     * @param array $item
     * @return string
     */
    public function getUpdate(DbTable $table, $item = array())
    {
        $sql = "UPDATE " . strtolower((string) $table) . " SET ";

        $values = array();
        foreach ($item as $field => $value) {
            $values[] = $this->parseValue($value);
            $sql .= $field. " = ?";
        }
        $sql = rtrim($sql, ',');

        return $sql;
    }

    /**
     * Returns a delete statement
     * @param Duality\Structure\DbTable $table
     * @return string
     */
    public function getDelete(DbTable $table)
    {
        $sql  = "DELETE FROM " . strtolower((string) $table) . " ";
        $sql .= ";";
        return $sql;
    }

}