Duality
=======

Micro PHP Framework


Why?
-----
Because PHP is powerful - whether you like it or not! :)

Demo
----
Usage demo repository at [Duality Demo](http://github.com/taviroquai/duality-demo)


Features
--------
1. Developed using high quality standards as OOP, PSR-2, Composer, phpunit, codesniffer and phpDocumentor
2. Structures and Services (duality universe philosophy)
3. App DI Container. Flexibility to integrate other services.
4. No imposition on templating library

Structures
----------
1. Table and database table - provides a mechanism to deal with tables
2. File - Image, Text and Stream
3. HTTP request and response
4. Entity - basic model in MVC
5. HtmlDoc - basic structure for HTML templating
6. URL - extended funcionality for URLs

Services
--------
1. Auth service - Uses Session service
2. Cache service - based on APCu
3. HTTP Client - based on cURL to make HTTP requests
4. Command line for non-web tasks - ie. database tasks
5. Database service - MySql and SQLite. TODO: PostgreSQL
6. Localization service - based on Intl php extension
7. Logger service - based on file stream
8. Mail service - PHPMailer
9. Paginator service - easy to deal with page numbers and page urls
10. SSH service - easy to run commands on remote machines
11. HTTP Server service - easy to deal with URI routes and HTTP request/responses
12. Session service - Native and Array
13. Validation service - easy to deal with custom input validation


Some Performance Stats (using demo repository)
----------------------------------------------

VM HOST: CPU Intel Core i3 3.2Ghz    
VM RAM: 1.5 Gb    
VM OS: Ubuntu 14.04 64 Bit    

```
Server Software:        Apache/2.4.7    
Server Hostname:        localhost    
Server Port:            80    

Document Path:          /duality-demo/    
Document Length:        2317 bytes    

Concurrency Level:      20    
Time taken for tests:   1.210 seconds    
Complete requests:      500    
Failed requests:        0    
Total transferred:      1264500 bytes    
HTML transferred:       1158500 bytes    
Requests per second:    413.21 #/sec (mean)    
Time per request:       48.402 ms (mean)    
Time per request:       2.420 ms (mean, across all concurrent requests)    
Transfer rate:          1020.50 Kbytes/sec received    
 
```
*Note: includes access to database*    


OOP, PSR-2, Composer, phpunit, codesniffer and phpDocumentor
-------------------------
Duality micro framework is developed using the most recent technologies
as PHP Object Oriented Programming approach. Composer as dependency manager.
Codesniffer and phpunit to maintain code quality. PhpDocumentor to validate doc blocks.
All code should consider PSR-2 conventions.

Structures and Services
--------------------------
All code is organized into to categories: structures and services
Structures define data limits and validation
Services are Core/User code that respondes to requests

In the demo, you will find a cmd.php that runs on the console like:

    php cmd.php db:seed

App DI Container
----------------
App is an high level DI container that gives flexibility to the user to register their services.
It also can provide a variety of common services used in web development, as:
auth, cache, http client, db, i18n, mailer, paginator, http server, session and validator

Usage example:
```
    $app->register('service', function() {
        return new MyService();
    }, $cacheable = true);
    $app->call('service');
```

Table structure
------------
Example:
```
    $table = $app->call('db')->getTable('users');
    $table->find(0, 10)->removeColumn('password');
    $items = $table->toArray();
```

File structure
------------
Example:
```
    $file = new \Duality\Structure\File\ImageFile('path/to/image.jpg');
    $file->saveThumb('path/to/thumb.jpg', $size = 60);

    $file = new \Duality\Structure\File\StreamFile('path/to/stream');
    $file->open('r+b');
    $file->load(function($chunk) {
        // Whatever... chunk is a 4096 bits
    });
    $file->close();
```

HTTP structure
------------
Example:
```
    $server = $app->call('server');
    $server->getResponse()->setHeaders(
        array('Content-Type', 'text/html')
    );
    $server->getResponse()->setCookies(array(
        array(
            'name'      => 'duality',
            'value'     => 'dummy',
            'expire'    => time(),
            'path'      => '/',
            'domain'    => 'duality.com',
            'secure'    => true
        )
    ));
    $server->send($server->getResponse());
```

Entity structure
------------
Example:
```
    class MyModel extends \Duality\Structure\Entity
    {
        protected $config = array(
            'properties' => array('id', 'name')
        );
    }
```

HtmlDoc structure
------------
Example:
```
    $doc = HtmlDoc::createFromFilePath('./data/template.html');
    $doc->appendTo(
        '//div[@class="page-header"]',
        '<h1 id="title">Welcome to Duality!</h1>'
    );
    echo $doc->save();
```

URL structure
------------
Example:
```
    $url = new \Duality\Structure\Url('https://user@domain.com:8080/uri?query#fragment');
    // url is now validated and splitten in parts
    echo $url; // Reconstructs full URL
```

Auth Service
------------
Example:
```
    $app->call('auth')->login($user, $pass, $storageCallback);
```

Cache Service
-------------
Example:
```
    $app->call('cache')->put('key', 'value', 'timestamp');
```

HTTP Client
-----------
Example:
```
    $client = new Client($app);
    $client->execute(Client::createRequest('http://google.com'));
```

Command-line for non-web tasks
------------------------------
Example:
```
    php cmd.php db:seed
```

Database Service
----------------
Example:
```
    $app->call('db')
        ->getTable('user')
        ->find(0, 10)
        ->toArray();
```

Localization Service
--------------------
Example:
```
    $app->call('i18n')
        ->setLocale('pt_PT')
        ->translate('key', array(), 'en_US');
```

Logger Service
--------------
Example:
```
    $app->call('logger')->log('my notice');
```

Mailer Service (PHPMailer by default)
-------------------------------------
Example:
```
    $app->call('mailer')
        ->setSMTP('smtp.google.com', 'user', 'pass')
        ->to('admin@domain.com')
        ->subject('My subject')
        ->body('Mail content...')
        ->send();
```

Paginator Service
-----------------
Example:
```
    $app->call('paginator')
        ->config($url, $totalItems, $itemsPerPage)
        ->getNextPageLink();
```

SSH Service
-----------
Example:
```
    $remote = new SSH($app);
    $remote->connect($host, $user, $pass);
    $remote->execute('ls');
```

HTTP Server Service
-------------------
Example:
```
    $app->call('server')
        ->addRoute('/^\/$/i', function(&$req, &$res) use ($app) {
            $res->setContent('Hello Duality!');
        });
```

Session Service
---------------
Example:
```
    $app->call('session')
        ->put('__lastError', 'Invalid input');
```

Validation Service
---------------
Example:
```
    $rules = array(
        'email' => array(
            'value' => $req->getParam('email'),
            'rules' => 'required|email',
            'fail'  => 'Invalid email address',
            'info'  => 'Email is valid'
        ),
        'pass'  => array(
            'value' => $req->getParam('pass'),
            'rules' => 'required|password',
            'fail'  => 'Invalid password: minimum 6 characters, with numbers, small and capital letters.',
            'info'  => 'Password is valid'  
        )
    );
    $app->call('validator')->validateAll($rules);
    $messages = $app->call('validator')->getMessages();
```

Future (Roadmap)
----------------
Depends on you... ;)
