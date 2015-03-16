<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

require_once './vendor/autoload.php';

// Create a new application container
$app = new Duality\App();

$logger = $app->getLogger();
$logger->log('teste');

// Create a new server
/*
try {
    $server = $app->getHTTPServer();
} catch (DualityException $e) {
	die($e->getMessage() . PHP_EOL);
}

// Define default route

$server->setHome(function(&$req, &$res) use ($app) {

	$res->setContent('Hello World!');
});

// Finaly, tell server to start listening
$server->execute();
*/