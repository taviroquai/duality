<?php
error_reporting(E_ALL);
ini_set('display_erros', true);

// Configure PSR-0 autoloaders
chdir('../');
spl_autoload_extensions('.php');
require_once './src/autoload.php';
require_once './demo/src/autoload.php';

// What will we use in our application?
use Demo\User;
use Duality\System\Database\SQLite;
use Duality\System\Structure\HtmlDoc;
use Duality\System\Structure\Url;
use Duality\System\Server;

// Create a server with hostname and base URL
$baseURL = new Url('/duality/demo');
$server = new Server('localhost', $baseURL);

// Get request from globals
$request = $server->getRequestFromGlobals();

// Create a default HTTP response
$response = $server->createResponse();

// Configure server with service /example/json
$server->addRoute('/\/example\/json/i', function(&$request, &$response) {
    
	// Create a default output
	$out = array('msg' => 'Example get data from database with ajax...', 'items' => array());
    
    try {
        // Create database
        $db = new SQLite('sqlite:./demo/data/db.sqlite');
    
        // Get a database table and its data from an entity
        $table = $db->createTableFromEntity(new User());
        $table->loadPage(0, 10);

        // Populate output with data
        $out['items'] = $table->toArray();

    } catch (\PDOException $e) {
        $out['msg'] = 'So bad! ' . $e->getMessage();
    }
    
	// Tell response to add HTTP content type header
	$response->addHeader('Content-type', 'application/json');
    
	// Tell response what is the output
	$response->setContent(json_encode($out));
    
});

// Configure default service
$server->addDefaultRoute(function(&$request, &$response) {

	// Create a new Document from a template file
	$doc = HtmlDoc::createFromFilePath('./demo/data/template.html');

	// Tell document to append new HTML content
	$doc->appendTo('//div[@id="container"]', '<h1 id="title">Hello Duality!</h1>');

	// Tell response what is the output
	$response->setContent($doc->save());

});

// Finally, tell server to execute services
$server->listen($request, $response);
