<?php

/**
 * Table filter structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.17.2
 */

namespace Duality\Structure\Database;

use Duality\Core\AbstractDatabaseTableFilter;
use Duality\Structure\Database\Table;

/**
 * Table filter class
 * 
 * Provides a filter for database tables
 * ie. \Duality\Structure\Database\Filter
 * Used by \Duality\Structure\Database\Filter
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.17.2
 */
class Filter
extends AbstractDatabaseTableFilter
{   
    /**
     * Creates a new table filter giving a database table
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     */
    public function __construct(Table $table)
    {
        parent::__construct($table);
    }

    /**
     * Sets the select parameter
     * 
     * @param string $columns The list of selected columns
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public function columns($columns)
    {
        $this->select = (string) $columns;
        return $this;
    }

    /**
     * Sets the where parameters
     * 
     * @param string $expression The where expression
     * @param array  $values     The array of values 
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public function where($expression, $values)
    {
        $this->whereExpr    = (string) $expression;
        $this->whereValues  = (array) $values;
        return $this;
    }

    /**
     * Sets the groupby parameter
     * 
     * @param string $columns The list of columns to group by
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public function group($columns)
    {
        $this->groupBy = (string) $columns;
        return $this;
    }

    /**
     * Sets the limit and offset parameters
     * 
     * @param int $total  The total number of rows
     * @param int $offset The number of rows to skip
     * 
     * @return \Duality\Structure\Database\Filter
     */
    public function limit($total, $offset = 0)
    {
        $this->limit    = (int) $total;
        $this->offset   = (int) $offset;
        return $this;
    }
}