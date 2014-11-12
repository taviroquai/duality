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

use Duality\Structure\Database\Table;

/**
 * Table filter class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.17.2
 */
class Filter
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
    protected $params;
    
    /**
     * Creates a new table filter giving a database table
     * 
     * @param \Duality\Structure\Database\Table $table The database table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;

        // Reset params
        $this->params = array(
            'select'        => '*',
            'where'         => array(
                'expr'      => '',
                'values'    => array()
            ),
            'groupby'       => '',
            'limit'         => array(
                'limit'     => null,
                'offset'    => 0
            )
        );
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
        $this->params['select'] = $columns;
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
        $this->params['where'] = array(
            'expr'      => $expression,
            'values'    => array()
        );
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
        $this->params['where'] = array(
            'expr'      => $columns,
            'values'    => array()
        );
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
        $this->params['limit'] = array(
            'total'     => (int) $total,
            'offset'    => (int) $offset
        );
        return $this;
    }

    /**
     * Gets the selected columns
     * 
     * @return string The selected columns
     */
    public function getSelect()
    {
        return (string) $this->params['select'];
    }

    /**
     * Gets the where expression
     * 
     * @return string The where expression
     */
    public function getWhere()
    {
        return (string) $this->params['where']['expr'];
    }

    /**
     * Gets the where values
     * 
     * @return array The where expression
     */
    public function getWhereValues()
    {
        return (array) $this->params['where']['values'];
    }

    /**
     * Gets the group by list of columns
     * 
     * @return string The list of columns
     */
    public function getGroupBy()
    {
        return (string) $this->params['groupby'];
    }

    /**
     * Gets the total items to limit
     * 
     * @return int The number of total items
     */
    public function getLimit()
    {
        return (int) $this->params['limit']['total'];
    }

    /**
     * Gets the total items to skip
     * 
     * @return int The number of total items to skip
     */
    public function getOffset()
    {
        return (int) $this->params['limit']['offset'];
    }

}