<?php

/**
 * Abstract entity structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Structure;

use Duality\Core\AbstractStructure;
use Duality\Core\AbstractDatabase;
use Duality\Structure\Storage;
use Duality\Structure\Database\Table;

/**
 * Entity class
 * 
 * Provides an interface for all Duality entities
 * It holds a properties storage for extended functionality
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
abstract class Entity 
extends AbstractStructure
{
    /**
     * Holds the entity configuration for extended functionality
     * 
     * @var array The list of configuration items
     */
    protected $config = array(
        'properties' => array('id')
    );

    /**
     * Holds the entity properties
     * 
     * @var \Duality\Structure\Storage The list of properties
     */
    protected $properties;

    /**
     * Holds the special ID property
     * 
     * @var string Holds the name of the property to identify
     */
    protected $defaultIdProperty = 'id';
    
    /**
     * The related database table
     * 
     * @var \Duality\Core\InterfaceDatabaseTable The related db table
     */
    protected $table;

    /**
     * Creates a new entity
     * 
     * @param \Duality\Service\Database $db The database storage
     */
    public function __construct(AbstractDatabase $db)
    {
        $this->properties = new Storage;
        if (!empty($this->config['name'])) {
            $this->setName($this->config['name']);
        }
        if (!empty($this->config['properties'])
            && is_array($this->config['properties'])
        ) {
            foreach ($this->config['properties'] as $item) {
                $this->addProperty(new Property($item));
            }
        }
        
        // Set database table
        if ($table = $db->getTable($this->getName())) {
            $this->setTable($table);
        }
    }

    /**
     * Adds a property to the entity
     * 
     * @param \Duality\Structure\Property $property The property to add
     * 
     * @return void
     */
    public function addProperty(Property $property)
    {
        $index = count($this->properties->asArray());
        $this->properties->add($index, $property);
    }

    /**
     * Gets all entity's properties
     * 
     * @return array The list of properties
     */
    public function getProperties()
    {
        return $this->properties->asArray();
    }

    /**
     * Adds properties from a list of properties
     * 
     * @param array $array Give an array of properties
     * 
     * @return void
     */
    public function addPropertiesFromArray($array)
    {
        foreach ((array) $array as $name) {
            $property = new Property((string) $name);
            $this->addProperty($property);
        }
    }
    
    /**
     * Sets the related database table
     * 
     * @since 1.0.1
     * 
     * @param \Duality\Structure\Table $table
     */
    public function setTable(Table $table)
    {
        $this->table = $table;
        $this->table->setColumnsFromEntity($this);
        $this->table->setPrimaryKey($this->defaultIdProperty);
    }

    /**
     * Returns the entity name
     * 
     * @return string Returns this entity name
     */
    public function __toString()
    {
        return $this->getName();
    }

}