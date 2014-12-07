<?php

require_once './vendor/autoload.php';

// Tell what our application uses
use Duality\App;

// Create a new application container
$app = new App(dirname(__FILE__));

// Create a new server
$server = $app->call('server');
$request = $server->getRequestFromGlobals($_SERVER, $_REQUEST);

// Validate request. This is a Web application.
if (!$request) {
	die('HTTP request not found!' . PHP_EOL);
}

// Load HTTP request from globals
$server->setRequest($request);

// Define default route
$server->setHome(function(&$req, &$res, $matches) {

	// Tell response what is the output
	$res->setContent('Hello World!');
});

// Finaly, tell server to start listening
$server->listen();
