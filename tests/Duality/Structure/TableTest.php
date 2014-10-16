<?php

class TableTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test table structure
     */
    public function testTable()
    {
        $table = new \Duality\Structure\Table();

        $column = new \Duality\Structure\Property('dummy');
        $table->addColumn($column);

        $row = new \Duality\Structure\TableRow($table);

        $row->setTable($table);
        $row->addData($column, 'value');
        $row->getData($column);

        $table->addRow($row);
        $table->getColumns();
        $table->getRows();
        $table->columnExists($column);
        $table->toCSV();
        $table->toArray();
    }
}