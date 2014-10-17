<?php

/**
 * Database service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Service;

use Duality\Core\DualityException;
use Duality\Core\AbstractService;
use Duality\Core\Structure;
use Duality\Structure\Property;
use Duality\Structure\Database\Table;
use Duality\Structure\Entity;

/**
 * Database class
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Database
extends AbstractService
{
    /**
     * Holds the PDO handler
     * 
     * @var \PDO
     */
    protected $pdo;

    /**
     * Holds the schema configuration
     * 
     * @var array
     */
    protected $schema;

    /**
     * Holds the database tables structures
     * 
     * @var array
     */
    protected $tables = array();

    /**
     * Initiates connection
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('db.dsn')) {
            throw new DualityException(
                "Error Config: db[dsn] not found", 1
            );
        }
        $dsn = $this->app->getConfigItem('db.dsn') ? 
            $this->app->getConfigItem('db.dsn') : '';
        $user = $this->app->getConfigItem('db.user') ? 
            $this->app->getConfigItem('db.user') : '';
        $pass = $this->app->getConfigItem('db.pass') ? 
            $this->app->getConfigItem('db.pass') : '';
        $options = $this->app->getConfigItem('db.options') ? 
            $this->app->getConfigItem('db.options') : array();

        if (empty($options)) {
            $options = array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            );
        }
        
        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    /**
     * Terminates connection
     * 
     * @return void
     */
    public function terminate()
    {
        $this->pdo = null;
    }

    /**
     * Adds a table to the database
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * 
     * @return void
     */
    public function addTable(Table $table)
    {
        $this->tables[(string) $table] = $table;
    }

    /**
     * Gets all database tables
     * 
     * @return array The list of database tables
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Gets the database connection
     * 
     * @return \PDO Returns the PDO instance
     */
    public function getPDO()
    {
        return $this->pdo;
    }

    /**
     * Creates a table from an Entity
     * 
     * @param \Duality\Structure\Entity $entity The entity structure
     * 
     * @return \Duality\Structure\Database\Table The database table
     */
    public function createTableFromEntity(Entity $entity)
    {
        // Get a database table and its data from an entity
        $table = new Table($this);
        $table->setColumnsFromEntity($entity);
        return $table;
    }

    /**
     * Validate schema configuration
     * 
     * @return array The database schema configuration
     */
    public function getSchemaConfig()
    {
        if (!$this->app->getConfigItem('db.schema') 
            || !file_exists($this->app->getConfigItem('db.schema'))
        ) {
            throw new DualityException("Missing schema configuration", 1);
        }
        return include($this->app->getConfigItem('db.schema'));
    }

    /**
     * Reload tables from configuration
     * 
     * @param array $config The schema configuration
     * 
     * @return void
     */
    public function reloadFromConfig($config)
    {
        $this->schema = $config;
        if (!isset($this->schema['create'])) {
            $this->schema['create'] = array();
        }
        if (!isset($this->schema['update'])) {
            $this->schema['update'] = array();
        }
        if (!isset($this->schema['seed'])) {
            $this->schema['seed'] = array();
        }
        $this->tables = array();
        foreach ($this->schema['create'] as $name => $fields) {
            $table = new Table($this);
            $table->setName($name);
            $columns = array_keys($fields);
            foreach ($columns as $name) {
                $table->addColumn(new Property($name));
            }
            $this->addTable($table);
        }
    }

    /**
     * Create schema definition from user config
     * 
     * @return void
     */
    public function createFromConfig()
    {   
        // Begin transation
        $this->pdo->beginTransaction();

        // Create tables
        foreach ($this->schema['create'] as $name => $schema) {
            $table = $this->tables[$name];
            $this->pdo->exec($this->getDropTable($table));
            $sql = $this->getCreateTable($table, $schema);
            $this->pdo->exec($sql);
        }

        // Commit changes
        $this->pdo->commit();
    }

    /**
     * Update schema definition from user configuration
     * 
     * @return void
     */
    public function updateFromConfig()
    {
        // Begin transation
        $this->pdo->beginTransaction();

        // Update tables
        foreach ($this->schema['update'] as $schema) {
            if (isset($this->tables[$schema['table']])) {
                $table = $this->tables[$schema['table']];
                $columns = $table->getColumns();
                if (isset($schema['drop'])) {
                    $sql = $this->getDropColumn($table, $schema['drop']);
                } elseif (isset($schema['add'])) {
                    $sql = $this->getAddColumn(
                        $table, $schema['add'], $schema['type']
                    );
                } elseif (isset($schema['modify']) 
                    && isset($columns[$schema['modify']])
                ) {
                    $sql = $this->getModifyColumn(
                        $table, $columns[$schema['modify']], $schema['type']
                    );
                }
                if (!empty($sql)) {
                    $this->pdo->exec($sql);
                }
            }
        }

        // Commit changes
        $this->pdo->commit();
    }

    /**
     * Seed database from configuration
     * 
     * @return void
     */
    public function seedFromConfig()
    {
        // Begin transation
        $this->pdo->beginTransaction();

        // Seed tables
        foreach ($this->schema['seed'] as $schema) {
            if (isset($this->tables[$schema['table']])) {
                $table = $this->tables[$schema['table']];
                if (isset($schema['truncate']) && $schema['truncate']) {
                    $sql = $this->getTruncate($table);
                }
                if (isset($schema['values'])) {
                    $sql = $this->getInsert($table, $schema['values']);
                    $stm = $this->pdo->prepare($sql);
                    $values = array();
                    foreach ($schema['values'] as $k => $v) {
                        $values[] = $this->parseValue($v);
                    }
                    $stm->execute($values);
                } else {
                    $this->pdo->exec($sql);
                }
            }
        }

        // Commit changes
        $this->pdo->commit();
    }

    /**
     * Parse input value
     * Database seed configuration can specify a value in the form: value::fn
     * fn options:
     * 1. int - Casts value to integer
     * 2. hash - Uses application encrypt to hash the value
     * 3. others (TODO)
     * 
     * @param string|int $value The value to be parsed
     * 
     * @return string|int The applied value by fn
     */
    protected function parseValue($value)
    {
        $catchFn = explode('::', $value);
        if (isset($catchFn[1])) {
            $fn = $catchFn[1];

            switch ($fn)
            {
            case 'hash': $value = $this->app->call('security')->encrypt($value); 
                break;
            case 'int': $value = (int) $value;
                break;
            default: $value = $catchFn[1];
            }
        }
        return $value;
    }

    /**
     * Loads a table structure from database
     * 
     * @param string $tablename The table name
     * 
     * @return string Returns the SQL statement
     */
    public function getTable($tablename)
    {
        $stm = $this->pdo->prepare($this->getColumns($tablename));
        $stm->execute();
        $info = $stm->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($info)) {
            throw new DualityException("Error table not found", 1);
        }
        $columns = array();
        foreach ($info as $item) {
            $columns[$item[$this->schema_column_name]]
                = $item[$this->schema_column_type];
        }
        $table = new Table($this);
        $table->setName($tablename);
        $table->setColumns($columns);
        return $table;
    }

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
    public abstract function getSelect(
        $fields, $from, $where = '', $limit = 0, $offset = 0
    );

    /**
     * Returns a create table statement
     * 
     * @param \Duality\Structure\Database\Table $table  The database table
     * @param array                             $config The table configuration
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getCreateTable(Table $table, $config);

    /**
     * Returns a drop table statement
     * 
     * @param Duality\Structure\DbTable $table    The database table
     * @param boolean                   $ifExists Adds IF EXISTS clause
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getDropTable(Table $table, $ifExists = true);

    /**
     * Returns a add column statement
     * 
     * @param \Duality\Structure\Database\Table $table      The database table
     * @param string                            $property   The column name
     * @param string                            $definition The table definition
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getAddColumn(Table $table, $property, $definition);

    /**
     * Returns a drop column statement
     * 
     * @param \Duality\Structure\Database\Table $table    The database table
     * @param string                            $property The column name
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getDropColumn(Table $table, $property);

    /**
     * Returns a add column statement
     * 
     * @param \Duality\Structure\Database\Table $table      The database table
     * @param \Duality\Structure\Property       $property   The column name
     * @param string                            $definition The table definition
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getModifyColumn(
        Table $table, Property $property, $definition
    );

    /**
     * Returns an INSERT statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * @param string                            $item  The item as array
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getInsert(Table $table, $item = array());

    /**
     * Returns an UPDATE statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * @param string                            $item  The item as array
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getUpdate(Table $table, $item = array());

    /**
     * Returns a DELETE statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * @param array                             $item  The item to be deleted
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getDelete(Table $table, $item);

    /**
     * Returns a TRUNCATE statement
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getTruncate(Table $table);

    /**
     * Returns a get columns statement
     * 
     * @param string $table The table name
     * 
     * @return string Returns the SQL statement
     */
    public abstract function getColumns($table);

}