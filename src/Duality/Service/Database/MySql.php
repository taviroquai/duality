<?php

/**
 * MySql query writer
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service\Database;

use Duality\Core\DualityException;
use Duality\Structure\Property;
use Duality\Structure\Database\Table;
use Duality\Service\Database;

/**
 * MySql database query writer
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class MySql extends Database
{
    /**
     * Returns a select query
     * 
     * @param string $fields The select clause
     * @param string $from   The from clause
     * @param string $where  The where condition - use ? for parameters
     * @param string $limit  The number of rows to limit
     * @param string $offset The offset number
     * 
     * @return string The final SQL string
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
     * 
     * @param \Duality\Structure\Database\Table $table  The database table
     * @param array                             $config The table configuration
     * 
     * @return string Returns the SQL statement
     */
    public function getCreateTable(Table $table, $config = array())
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
     * 
     * @param Duality\Structure\DbTable $table    The database table
     * @param boolean                   $ifExists Adds IF EXISTS clause
     * 
     * @return string Returns the SQL statement
     */
    public function getDropTable(Table $table, $ifExists = true)
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
     * 
     * @param \Duality\Structure\Database\Table $table      The database table
     * @param string                            $property   The column name
     * @param string                            $definition The table definition
     * 
     * @return string Returns the SQL statement
     */
    public function getAddColumn(Table $table, $property, $definition)
    {
        $sql  = "ALTER TABLE " . strtolower((string) $table) . " ";
        $sql .= "ADD COLUMN " . strtolower((string) $property) . " ";
        $sql .= $definition;
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns a add column statement
     * 
     * @param \Duality\Structure\Database\Table $table      The database table
     * @param string                            $property   The column name
     * @param string                            $definition The table definition
     * 
     * @return string Returns the SQL statement
     */
    public function getModifyColumn(Table $table, Property $property, $definition)
    {
        $sql  = "ALTER TABLE " . strtolower((string) $table) . " ";
        $sql .= "MODIFY COLUMN " . strtolower((string) $property) . " ";
        $sql .= $definition;
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns an INSERT statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * @param string                            $item  The item as array
     * 
     * @return string Returns the SQL statement
     */
    public function getInsert(Table $table, $item = array())
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
     * Returns an UPDATE statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * @param string                            $item  The item as array
     * 
     * @return string Returns the SQL statement
     */
    public function getUpdate(Table $table, $item = array())
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
     * Returns a DELETE statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * @param array                             $item  The item to be deleted
     * 
     * @return string Returns the SQL statement
     */
    public function getDelete(Table $table, $item)
    {
        $sql  = "DELETE FROM " . strtolower((string) $table) . " ";
        $sql .= "WHERE id = ?";
        return $sql;
    }

    /**
     * Returns a TRUNCATE statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * 
     * @return string Returns the SQL statement
     */
    public function getTruncate(Table $table)
    {
        $sql  = "TRUNCATE " . strtolower((string) $table) . " ";
        return $sql;
    }

}