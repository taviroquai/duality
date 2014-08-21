<?php

// Configure PSR-0 autoloader
chdir('../');
require_once 'autoload.php';
spl_autoload_extensions('.php');
spl_autoload_register('autoload');

// What will we use in our application?
use Duality\System\Structure\Http;
use Duality\System\File\TextFile;
use Duality\System\Server;
use Duality\System\Structure\HtmlDoc;
use Duality\System\Ajax;

// Create a server, load request and create default response
$server = new Server('localhost', '/duality/demo');
$request = new Http;
$request->parseFromGlobals();
$response = $server->createResponse();

// Configure server services with routes
$server->addRoute('/\/example\/json/i', function(&$request, &$response) use ($server) {
	$response->addHeader('Content-type', 'application/json');
	$response->setContent(json_encode(array('result' => true, 'msg' => 'Ajax!')));
});

// Create default server response (an HTML document)
$server->addRoute('/\//i', function(&$request, &$response) use ($server) {
	$doc = new HtmlDoc();
	$template = new TextFile('./demo/data/template.html');
	$template->getContent();
	$doc->loadFile($template);
	$doc->appendTo('//div[@id="container"]', '<h1 id="title">Hello Duality!</h1>');
	$response->setContent($doc->save());
});

// Finally, start server listen
$server->listen($request, $response);
