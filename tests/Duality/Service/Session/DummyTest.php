<?php

class DummyTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test dummy session service
     */
    public function testDummySession()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $session = $app->call('session');

        $session->add('dummy', 'dummy');
        $session->set('dummy', 'dummy');
        $session->get('dummy');
        $session->has('dummy');
        $session->asArray();
        $session->remove('dummy');
        $session->importArray(array('dummy', 'dummy'));
        $session->reset();
        $session->terminate();
    }
}