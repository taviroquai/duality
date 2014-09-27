<?php

class DataTest extends PHPUnit_Framework_TestCase {

	public function testValue()
	{
		$expected = true;
		$data = new \Duality\Core\Data();
		$data->setValue($expected);
		$this->assertEquals($expected, $data->getValue());
	}
}