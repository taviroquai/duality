<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once './vendor/autoload.php';

// Create a new application container
$app = new Duality\App();

// Create a new server
$server = $app->getHTTPServer();

// Define default route
$server->setHome(function(&$req, &$res) {
	$res->setContent('Hello World!');
});

// Finaly, tell server to start listening
$server->execute();
