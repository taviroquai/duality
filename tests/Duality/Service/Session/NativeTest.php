<?php

class NativeTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test native session service
     * 
     * @runInSeparateProcess
     */
    public function testNativeSession()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Native'
            )
        );
        $app = new \Duality\App(dirname(__FILE__), $config);
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