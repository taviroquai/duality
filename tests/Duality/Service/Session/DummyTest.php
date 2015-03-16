<?php

class DummyTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test dummy session service
     */
    public function testDummySession()
    {
        $app = new \Duality\App();
        $session = $app->call('session');

        $expected = 'dummy';
        $session->add('dummy', $expected);
        $session->set('dummy', $expected);
        $result = $session->get('dummy');
        $this->assertEquals($expected, $result);

        $expected = TRUE;
        $result = $session->has('dummy');
        $this->assertEquals($expected, $result);

        $expected = array('dummy' => 'dummy');
        $result = $session->asArray();
        $this->assertEquals($expected, $result);

        $expected = array();
        $session->remove('dummy');
        $result = $session->asArray();
        $this->assertEquals($expected, $result);
        
        $expected = 'dummy';
        $session->add('dummy', $expected);
        $result = $session->take('dummy');
        $this->assertEquals($expected, $result);

        $expected = array('dummy', 'dummy');
        $session->importArray($expected);
        $result = $session->asArray();
        $this->assertEquals($expected, $result);

        $expected = array();
        $session->reset();
        $result = $session->asArray();
        $this->assertEquals($expected, $result);

        $session->terminate();
    }
}