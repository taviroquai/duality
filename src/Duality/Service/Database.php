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

use Duality\Core\InterfaceService;
use Duality\Core\Structure;
use Duality\Structure\Property;
use Duality\Structure\Database\Table;
use Duality\Structure\Entity;
use Duality\App;

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
implements InterfaceService
{
    /**
     * The dependent application container
     * 
     * @var \Duality\App The application container
     */
    protected $app;

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
     * Creates a new error handler
     * 
     * @param \Duality\App &$app The application container
     */
    public function __construct(App &$app)
    {
        $this->app = $app;
    }

    /**
     * Initiates connection
     * 
     * @return void
     */
    public function init()
    {
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
     * Temrinates connection
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
            throw new Exception("Missing schema configuration", 1);
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

        $this->tables = array();
        foreach ($config['create'] as $name => $fields) {
            $table = new DbTable($this);
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
        $config = $this->getSchemaConfig();
        
        // Begin transation
        $this->pdo->beginTransaction();

        // Reload tables
        $this->reloadFromConfig($config);

        // Create tables
        foreach ($config['create'] as $name => $item) {
            $table = $this->tables[$name];
            $this->pdo->exec($this->getDropTable($table));
            $sql = $this->getCreateTable($table, $item);
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
        $config = $this->getSchemaConfig();

        // Begin transation
        $this->pdo->beginTransaction();

        // Reload tables
        $this->reloadFromConfig($config);

        // Update tables
        foreach ($config['update'] as $item) {
            if (isset($this->tables[$item['table']])) {
                $table = $this->tables[$item['table']];
                $columns = $table->getColumns();
                if (isset($item['add'])) {
                    $this->pdo->exec(
                        $this->getAddColumn($table, $item['add'], $item['type'])
                    );
                } elseif (isset($item['modify']) 
                    && isset($columns[$item['modify']])
                ) {
                    $this->pdo->exec(
                        $this->getModifyColumn(
                            $table, $columns[$item['modify']], $item['type']
                        )
                    );
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
        $config = $this->getSchemaConfig(); 
        
        // Begin transation
        $this->pdo->beginTransaction();

        // Reload tables
        $this->reloadFromConfig($config);

        // Seed tables
        foreach ($config['seed'] as $item) {
            if (isset($this->tables[$item['table']])) {
                $table = $this->tables[$item['table']];
                if (isset($item['truncate']) && $item['truncate']) {
                    $this->pdo->exec($this->getTruncate($table));
                }
                if (isset($item['values'])) {
                    $sql = $this->getInsert($table, $item['values']);
                    $stm = $this->pdo->prepare($sql);
                    $values = array();
                    foreach ($item['values'] as $k => $v) {
                        $values[] = $this->parseValue($v);
                    }
                    $stm->execute($values);
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
            case 'hash': $value = $this->app->encrypt($value); 
                break;
            case 'int': $value = (int) $value;
                break;
            default: $value = $catchFn[1];
            }
        }
        return $value;
    }

}