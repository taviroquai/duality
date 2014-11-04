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

        $expected = "Command not found\n";
        $cmd->setInput('');
        ob_start();
        $cmd->listen();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);

        $expected = 'response';
        $cmd->setInput('dummy.php dummy');
        ob_start();
        $cmd->listen();
        $result = ob_get_clean();
        $this->assertEquals($expected, $result);

        $cmd->terminate();
    }
}