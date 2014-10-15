<?php

class SSHTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test failed config
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidHost()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->connectSSH('localhos');
    }

    /**
     * Test failed config
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidUser()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'dummy');
    }

    /**
     * Test failed config
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidAuth()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'duality', 'wrong');
    }

    /**
     * Test failed config
     * 
     * @expectedException \Duality\Core\DualityException
     */
    public function testInvalidFingerprint()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'duality', 'dummy', 22, null, 'dummy');
    }

    /**
     * Test failed config
     */
    public function testInvalidConnection()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->execute('ls');
    }

    /**
     * Test failed config
     */
    public function testInvalidcommand()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->connectSSH('localhost', 'duality', 'dummy');
        $remote->execute('badass');
    }

    /**
     * Test SSH service
     */
    public function testSSH()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
        $remote->init();

        $remote->connectSSH('localhost','duality','dummy');
        $remote->execute('ls');
        $remote->terminate();
    }
}