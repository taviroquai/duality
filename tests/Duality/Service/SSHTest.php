<?php

use Duality\Service\SSH;

class SSHTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test failed config
     *
     * @requires extension ssh2
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHost()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->connectSSH('localhos', 'dummy');
    }

    /**
     * Test failed config
     * 
     * @requires extension ssh2
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidUser()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'dummy');
    }

    /**
     * Test failed config
     * 
     * @requires extension ssh2
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidAuth()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'duality', 'wrong');
    }

    /**
     * Test failed config
     * 
     * @requires extension ssh2
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidFingerprint()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'duality', 'dummy', 22, null, 'dummy');
    }

    /**
     * Test failed config
     * 
     * @requires extension ssh2
     */
    public function testInvalidConnection()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->execute('ls');
    }

    /**
     * Test failed config
     * 
     * @requires extension ssh2
     */
    public function testInvalidcommand()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'duality', 'dummy');
        $remote->execute('badass');
    }

    /**
     * Test SSH service
     * 
     * @requires extension ssh2
     */
    public function testSSH()
    {
        $app = new \Duality\App();
        $remote = new SSH($app);
        $remote->init();

        $remote->connectSSH('localhost','duality','dummy');
        $remote->execute('ls');
        $remote->terminate();
    }
}