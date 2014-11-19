<?php

/**
 * Table filter interface
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.17.2
 */

namespace Duality\Core;

use Duality\Structure\Database\Table;

/**
 * Table filter interface
 * 
 * Provides an interface for all db table filters
 * ie. \Duality\Structure\Database\Filter
 * Used by \Duality\Structure\Database\Filter
 * Used by \Duality\Structure\Database\Table
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.17.2
 */
abstract class AbstractDatabaseTableFilter
{
    /**
     * Holds the database service instance
     * 
     * @var \Duality\Sstructure\Database\Table The database table
     */
    protected $table;

    /**
     * Holds the select parameter
     * 
     * @var array The list of columns to select
     */
    protected $select;
    
    /**
     * Holds the select parameter
     * 
     * @var string The where expression
     */
    protected $whereExpr;
    
    /**
     * Holds the where values
     * 
     * @var array The list of where values
     */
    protected $whereValues;
    
    /**
     * Holds the group by parameter
     * 
     * @var string The group by list of columns
     */
    protected $groupBy;
    
    /**
     * Holds the limit parameter
     * 
     * @var int The limit number of returned rows
     */
    protected $limit;
    
    /**
     * Holds the offset parameter
     * 
     * @var int The offset number to start rows
     */
    protected $offset;
    
    /**
     * Creates a new table filter giving a database table
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        // Reset attributes
        $this->select       = '';
        $this->whereExpr    = '';
        $this->whereValues  = array();
        $this->groupBy      = '';
        $this->limit        = null;
        $this->offset       = 0;
    }

    /**
     * Sets the select parameter
     * 
     * @param string $columns The list of selected columns
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public abstract function columns($columns);

    /**
     * Sets the where parameters
     * 
     * @param string $expression The where expression
     * @param array  $values     The array of values 
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public abstract function where($expression, $values);

    /**
     * Sets the groupby parameter
     * 
     * @param string $columns The list of columns to group by
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public abstract function group($columns);

    /**
     * Sets the limit and offset parameters
     * 
     * @param int $total  The total number of rows
     * @param int $offset The number of rows to skip
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public abstract function limit($total, $offset = 0);

    /**
     * Gets the selected columns
     * 
     * @return string The selected columns
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Gets the where expression
     * 
     * @return string The where expression
     */
    public function getWhere()
    {
        return $this->whereExpr;
    }

    /**
     * Gets the where values
     * 
     * @return array The where expression
     */
    public function getWhereValues()
    {
        return $this->whereValues;
    }

    /**
     * Gets the group by list of columns
     * 
     * @return string The list of columns
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * Gets the total items to limit
     * 
     * @return int The number of total items
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Gets the total items to skip
     * 
     * @return int The number of total items to skip
     */
    public function getOffset()
    {
        return $this->offset;
    }

}