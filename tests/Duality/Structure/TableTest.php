<?php

use Duality\Structure\Property;
use Duality\Structure\TableRow;
use Duality\Structure\Table;

class TableTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test table structure
     */
    public function testTable()
    {
        $table = new Table();

        $column = new Property('dummy');
        $table->addColumn($column);

        $row = new TableRow($table);
        $row->setTable($table);

        $expected = 'value';
        $row->addData($column, $expected);
        $result = $row->getData($column);
        $this->assertEquals($expected, $result);

        $expected = array(array('dummy' => $expected));
        $table->addRow($row);
        $this->assertEquals($table->toArray(), $expected);

        $expected = array('dummy' => $column);
        $result = $table->getColumns();
        $this->assertEquals($expected, $result);

        $expected = TRUE;
        $result = $table->columnExists($column);
        $this->assertEquals($expected, $result);

        $expected = array(
            array('dummy' => 'value')
        );
        $table->reset();
        $column = new Property('dummy');
        $table->addColumn($column);
        $table->importArray($expected);
        $result = $table->toArray();
        $this->assertEquals($expected, $result);

        $expected = "dummy\nvalue\n";
        $result = $table->toCSV();
        $this->assertEquals($expected, $result);

        $expected = array(array());
        $table->removeColumn('dummy');
        $result = $table->toArray();
        $this->assertEquals($expected, $result);
    }
}