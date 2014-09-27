<?php

/**
 * Database service
 *
 * @since       0.7.0
 * @author      Marco Afonso <mafonso333@gmail.com>
 * @license     MIT
 */

namespace Duality\Service;

use Duality\Core\InterfaceService;
use Duality\Core\Structure;
use Duality\Structure\Property;
use Duality\Structure\DbTable;
use Duality\Structure\Entity;
use Duality\App;

/**
 * Database class
 */
abstract class Database 
extends Structure 
implements InterfaceService
{
    /**
     * Holds application container
     * @var Duality\App
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
     * Creates a new database service
     * @param Duality\App $app
     */
	public function __construct(App &$app)
	{
        $this->app = $app;
	}

    /**
     * Initiates connection
     */
    public function init()
    {
        $dsn = $this->app->getConfigItem('db.dsn') ? $this->app->getConfigItem('db.dsn') : '';
        $user = $this->app->getConfigItem('db.user') ? $this->app->getConfigItem('db.user') : '';
        $pass = $this->app->getConfigItem('db.pass') ? $this->app->getConfigItem('db.pass') : '';
        $options = $this->app->getConfigItem('db.options') ? $this->app->getConfigItem('db.options') : array();

        if (empty($options)) {
            $options = array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            );
        }
        $this->pdo = new \PDO($dsn, $user, $pass, $options);
    }

    /**
     * Temrinates connection
     */
    public function terminate()
    {
        $this->pdo = NULL;
    }

    /**
     * Adds a table to the database
     * @param \Duality\Structure\DbTable $table
     */
	public function addTable(DbTable $table)
	{
		$this->tables[(string) $table] = $table;
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

    /**
     * Creates a table from an Entity
     * @param Duality\Structure\Entity $entity
     */
    public function createTableFromEntity(Entity $entity)
    {
        // Get a database table and its data from an entity
        $table = new DbTable($this);
        $table->setPropertiesFromEntity($entity);
        return $table;
    }

    /**
     * Validate schema configuration
     */
    public function getSchemaConfig()
    {
        if (
            !$this->app->getConfigItem('db.schema') 
            || !file_exists($this->app->getConfigItem('db.schema'))
        ) {
            throw new Exception("Missing schema configuration", 1);
        }
        return include($this->app->getConfigItem('db.schema'));
    }

    /**
     * Reload tables from configuration
     * @param array $config
     */
    public function reloadFromConfig($config)
    {

        $this->tables = array();
        foreach ($config['create'] as $name => $fields) {
            $table = new DbTable($this);
            $table->setName($name);
            $props = array_keys($fields);
            foreach ($props as $name) {
                $table->addProperty(new Property($name));
            }
            $this->addTable($table);
        }
    }

    /**
     * Create schema definition from user config
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
     * Update schema definition from user config
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
                $props = $table->getProperties();
                if (isset($item['add'])) {
                    $this->pdo->exec($this->getAddColumn($table, $item['add'], $item['type']));
                } elseif (isset($item['modify']) && isset($props[$item['modify']])) {
                    $this->pdo->exec($this->getModifyColumn($table, $props[$item['modify']], $item['type']));
                }
            }
        }

        // Commit changes
        $this->pdo->commit();
    }

    /**
     * Seed from user config
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
                if (isset($item['delete']) && $item['delete']) {
                    $this->pdo->exec($this->getDelete($table));
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
     * @param string|int $value
     * @return string|int
     */
    protected function parseValue($value)
    {
        $catchFn = explode('::', $value);
        if (isset($catchFn[1])) {
            $fn = $catchFn[1];
            switch($fn) {
            case 'hash': $value = $this->app->encrypt($value); break;
            case 'int': $value = (int) $value; break;
            default: $value = $catchFn[1];
            }
        }
        return $value;
    }

}