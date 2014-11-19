<?php

/**
 * Table row structure
 *
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */

namespace Duality\Core;

use Duality\Structure\Table;
use Duality\Structure\Property;

/**
 * Table row class
 * 
 * PHP Version 5.3.4
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.7.0
 */
interface InterfaceTableRow
{
    /**
     * Creates a new table row
     * 
     * @param \Duality\Structure\Table $table The row table
     */
    public function __construct(Table $table);

    /**
     * Gets the dependant table
     * 
     * @return \Duality\Structure\Table $table The table which belongs
     */
    public function getTable();

    /**
     * Adds data to the row
     * 
     * @param \Duality\Structure\Property $property The property
     * @param string|int                  $data     The data to be added
     * 
     * @return void
     */
    public function addData(Property $property, $data);

    /**
     * Gets the row property data
     * 
     * @param \Duality\Structure\Property $property Give the property to identify
     * 
     * @return string|int The result data
     */
    public function getData(Property $property);
    
}