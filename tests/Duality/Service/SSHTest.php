<?php

class SSHTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test invalid auth service
     * 
     * @requires extension ssh2
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidAuth()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Dummy',
                'auth' => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
                'ssh' => array(
                    'host' => 'localhost'
                )
            )
        );
        $app = new \Duality\App($config);
        $remote = $app->call('remote');
        
        $remote->execute('badass1');
    }
    
    /**
     * Test SSH service
     * 
     * @requires extension ssh2
     */
    public function testCommand()
    {
        $config = array(
            'services' => array(
                'session' => '\Duality\Service\Session\Dummy',
                'auth' => '\Duality\Service\Auth\SSH'
            ),
            'auth' => array(
                'ssh' => array(
                    'host' => 'localhost'
                )
            )
        );
        $app = new \Duality\App($config);
        $app->call('auth')->login('duality', 'dummy');
        $remote = $app->call('remote');

        $remote->execute('ls');
        $remote->terminate();
    }
}