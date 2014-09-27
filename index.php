<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once './vendor/autoload.php';

// Tell what our application uses
use Duality\Service\Server;
use Duality\App;

// Setup configuration
$config = array(
    'server' => array(
        'url' => '/duality',
        'hostname' => 'localhost'
    )
);

// Create a new application container
$app = new App(dirname(__FILE__), $config);

// Create a new server and initiate defaults
$server = new Server($app);
$server->init();

// Define default route
$server->setHome(function(&$req, &$res, $matches) {

	// Tell response what is the output
	$res->setContent('Hello World!');
});

// Finaly, tell server to start listening
$server->listen();