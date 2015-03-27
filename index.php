<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once './vendor/autoload.php';

// Set class aliases
use Duality\Core\InterfaceUrl;
use Duality\Structure\Http\Request;
use Duality\Structure\Http\Response;
use Duality\App;

// Create optional request
class MyRequest
extends Request
{
    public function __construct(InterfaceUrl $url) {
        parent::__construct($url);
        $this->importFromGlobals();
    }
}

// Create optional response
class Home
extends Response
{
    public function onRequest(Request $req)
    {
        $this->setContent('Hello World!');
    }
}

// Create required response
class CatchAll
extends Response
{
    public function onRequest(Request $req)
    {
        $this->setContent('Default response');
    }
}

// Create a new application container
$app = new App();

// Create a new server
$server = $app->getHTTPServer();

// Set catch all response
$server->setResponse(new CatchAll());

// Define optional home route
$server->setHome('Home', 'MyRequest');

// Define other optional routes
$server->addRoute('/^\/test$/i', 'Home');

// Finaly, tell server to execute
$server->execute();
