<?php

/**
 * SQLIte query writer
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service\Database;

use Duality\Structure\Property;
use Duality\Structure\Database\Table;
use Duality\Service\Database;

/**
 * SQLite database query writer
 * 
 * Provides SQLite database operations
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
class SQLite extends Database
{
    /**
     * Holds the information schema column name
     * 
     * @var string
     */
    protected $schema_column_name = 'name';

    /**
     * Holds the information schema column type
     * 
     * @var string
     */
    protected $schema_column_type = 'type';

    /**
     * Returns a select query
     * 
     * @param string $fields  The select clause
     * @param string $from    The from clause
     * @param string $where   The where condition - use ? for parameters
     * @param string $groupby The groupby clause
     * @param string $limit   The number of rows to limit
     * @param string $offset  The offset number
     * 
     * @return string The final SQL string
     */
    public function getSelect(
        $fields, $from, $where = '', $groupby = '', $limit = 0, $offset = 0
    ) {
        $sql = "SELECT $fields FROM ".strtolower((string) $from);
        if (!empty($where)) {
            $sql .= " WHERE ".$where;
        }
        if ($limit > 0) {
            $sql .= ' LIMIT '.$limit.' OFFSET '.$offset;
        }
        $sql .= ';';
        return $sql;
    }

    /**
     * Returns a create table statement
     * 
     * @param \Duality\Structure\Table $table  The database table
     * @param array                                $config The table config
     * 
     * @return string Returns the SQL statement
     */
    public function getCreateTable(Table $table, $config)
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . strtolower((string) $table) . " (";

        foreach ($config as $field => $definition) {
            if ($definition == 'auto') {
                $definition = 'INTEGER PRIMARY KEY';
            }
            $sql .= $field . " " . $definition . ", ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= ');';
        
        return $sql;
    }

    /**
     * Returns a drop table statement
     * 
     * @param \Duality\Structure\Table $table  The database table
     * @param boolean                              $ifExists Adds IF EXISTS
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
     * @param \Duality\Structure\Table $table    The database table
     * @param string                               $property The column name
     * @param string                               $def      The table definition
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
     * Returns a drop column statement
     * 
     * @param \Duality\Structure\Table $table    The database table
     * @param string                               $property The column name
     * 
     * @return string Returns the SQL statement
     */
    public function getDropColumn(Table $table, $property)
    {
        // Not implemented in sqlite
        return false;
    }

    /**
     * Returns a add column statement
     * 
     * @param \Duality\Structure\Table $table      The database table
     * @param \Duality\Structure\Property          $property   The column name
     * @param string                               $definition The table definition
     * 
     * @return string Returns the SQL statement
     */
    public function getModifyColumn(
        Table $table, Property $property, $definition
    ) {
        // Not implemented in sqlite
        return false;
    }

    /**
     * Returns an INSERT statement
     * 
     * @param \Duality\Structure\Table $table The database table
     * @param string                               $item  The item as array
     * 
     * @return string Returns the SQL statement
     */
    public function getInsert(Table $table, $item = array())
    {
        $sql = "INSERT INTO " . strtolower((string) $table) . " (";

        $values = array();
        foreach ($item as $field => $value) {
            $values[] = $this->parseValue($value);
            $sql .= $field. ",";
        }
        $sql = rtrim($sql, ',');
        $sql .= ') ';

        if (!empty($values)) {
            $sql .= 'VALUES (';
            foreach ($values as $item) {
                $sql .= '?,';
            }
            $sql = rtrim($sql, ',');
            $sql .= ')';
        }
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns an UPDATE statement
     * 
     * @param \Duality\Structure\Table $table The database table
     * @param string                               $item  The item as array
     * 
     * @return string Returns the SQL statement
     */
    public function getUpdate(Table $table, $item = array())
    {
        $sql = "UPDATE " . strtolower((string) $table) . " SET ";

        $values = array();
        foreach ($item as $field => $value) {
            $values[] = $this->parseValue($value);
            $sql .= $field. " = ?,";
        }
        $sql = rtrim($sql, ',');
        $sql .= " WHERE " . $table->getPrimaryKey() . ' = ?';
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns a DELETE statement
     * 
     * @param \Duality\Structure\Table $table The database table
     * @param array                                $item  The item to be deleted
     * 
     * @return string Returns the SQL statement
     */
    public function getDelete(Table $table, $item)
    {
        $sql  = "DELETE FROM " . strtolower((string) $table) . " ";
        $sql .= "WHERE ";
        $values = array();
        foreach ($item as $field => $value) {
            $values[] = $this->parseValue($value);
            $sql .= $field. " = ?";
        }
        $sql = rtrim($sql, ',');
        $sql .= ";";
        return $sql;
    }

    /**
     * Returns a TRUNCATE statement
     * 
     * @param \Duality\Structure\Table $table The database table
     * 
     * @return string Returns the SQL statement
     */
    public function getTruncate(Table $table)
    {
        // Not implemented in sqlite
        return 'DELETE FROM ' . (string) $table;
    }

    /**
     * Returns a get columns statement
     * 
     * @param string $tablename The database table name
     * 
     * @return string Returns the SQL statement
     */
    public function getColumns($tablename)
    {
        $sql = "PRAGMA table_info('$tablename');";
        return $sql;
    }
}