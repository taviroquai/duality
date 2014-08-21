<?php

// Configure PSR-0 autoloader
chdir('../');
require_once 'autoload.php';
spl_autoload_extensions('.php');
spl_autoload_register('autoload');

// What will we use in our application?
use Duality\System\File\TextFile;
use Duality\System\Structure\HtmlDoc;
use Duality\System\Structure\Http;
use Duality\System\Server;

// Create a server with hostname and base URL
$server = new Server('localhost', '/duality/demo');

// Create an HttP request
$request = new Http;

// Parse request from globals
$request->parseFromGlobals();

// Create an HTTP response
$response = $server->createResponse();

// Configure server with route /example/json
$server->addRoute('/\/example\/json/i', function(&$request, &$response) use ($server) {

	// Tell response to add HTTP content type header
	$response->addHeader('Content-type', 'application/json');

	// Tell response what is the output
	$response->setContent(json_encode(array('result' => true, 'msg' => 'Ajax!')));

});

// Create default server response (an HTML document)
$server->addRoute('/\//i', function(&$request, &$response) use ($server) {

	// Create a new Document
	$doc = new HtmlDoc();

	// Create a template file by telling its path
	$template = new TextFile('./demo/data/template.html');

	// Tell to template to load its contents
	$template->getContent();

	// Tell Document to import from file
	$doc->loadFile($template);

	// Tell Document to append HTML to the container element
	$doc->appendTo('//div[@id="container"]', '<h1 id="title">Hello Duality!</h1>');

	// Tell response what is the output
	$response->setContent($doc->save());
	
});

// Finally, start server listen
$server->listen($request, $response);
