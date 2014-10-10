<?php

class SSHTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test paginator service
     */
    public function testSSH()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $remote = new \Duality\Service\SSH($app);
    }
}