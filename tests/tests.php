<?php


error_reporting(E_ALL);
ini_set('display_errors', true);

chdir('../');
require_once 'autoload.php';
spl_autoload_extensions('.php');
spl_autoload_register('autoload');

use Duality\System\Structure\Property;
use Duality\System\Structure\Entity;
use Duality\System\Database\MySql;
use Duality\System\Structure\DbTable;
use Duality\System\Structure\Http;
use Duality\System\File\TextFile;
use Duality\System\File\StreamFile;
use Duality\System\Server;
use Duality\System\Client;
use Duality\System\Structure\HtmlDoc;

$db = new MySql('mysql:host=localhost;dbname=test', 'root', 'toor');

$user = new Entity(new Property('id'));
$user->setName('user');
$user->addProperty(new Property('email'));
$user->addProperty(new Property('password'));

$table = new DbTable($db);
$table->loadFromEntity($user);

$file = new TextFile('./tests/data/example.csv');
$file->setContent($table->toCSV());
$file->save();

$binary = new StreamFile('./tests/data/binary');
$binary->open();
$binary->setContent($table->toCSV());
$binary->save();
$binary->close();

$server = new Server('localhost', '/duality/tests');
$url = $server->createUrl('/duality/tests/?testcurl=value1');
$client = new Client;
$request = $client->createRequest($url);

$doc = new HtmlDoc();
$template = new TextFile('./tests/data/template.html');
$template->getContent();
$doc->loadFile($template);
$doc->setTitle('Text HTML document');
$doc->appendTo('body', '<h1 id="title">Hello World!</h1>');
$file = new TextFile('./tests/data/example.html');
$file->setContent($doc->save());
$file->save();

$request = new Http;
$request->parseFromGlobals();
$response = $server->createResponse();
$server->addRoute('/\/route\/example/i', function(&$request, &$response) use ($server, $client) {

	if (!isset($_GET['testcurl'])) {
		$client->execute($request);
		$response->setContent($doc->save());
	} else {
		$response->setContent('Test curl');
	}

	$response->addHeader('Content-type', 'application/json');
	$response->setContent(json_encode(array('result' => true, 'msg' => 'Ajax!')));
});

$server->listen($request, $response);
