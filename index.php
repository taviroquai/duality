<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once './vendor/autoload.php';

// Set class aliases
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;
use Duality\App;

// Create a requestable http response
class MyResponse
extends Response
{
    public function onRequest(Request $req)
    {
        $this->setContent('Hello World!');
    }
}

// Create a new application container
$app = new App();

// Create a new server
$server = $app->getHTTPServer();

// Define default route
$server->setHome('MyResponse');

// Finaly, tell server to start listening
$server->execute();
