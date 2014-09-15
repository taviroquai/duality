<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once './vendor/autoload.php';

// Tell what our application uses
use Duality\System\Structure\Url;
use Duality\System\Server;

// Create a new server
$server = new Server('localhost', new Url('/duality'));

// Define default route
$server->addDefaultRoute(function(&$req, &$res) {

	// Tell response what is the output
	$res->setContent('Hello World!');
});

// Finaly, tell server to start listening
$server->listen();