<?php

class CommanderTest 
extends PHPUnit_Framework_TestCase
{
    /**
     * Test commander service
     */
    public function testCommander()
    {
        $app = new \Duality\App(dirname(__FILE__), null);
        $cmd = $app->call('cmd');

        $cmd->addResponder('/dummy/', function () {
            echo 'response';
        });

        $cmd->setInput('');
        $cmd->listen();

        $cmd->setInput('dummy.php dummy');
        $cmd->listen();

        $cmd->terminate();
    }
}