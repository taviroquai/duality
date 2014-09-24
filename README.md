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
1. OOP, Composer, phpunit and PSR-2
2. Structures and Services (duality universe philosophy)
3. App DI Container. Flexibility to integrate services.
4. Auth service
5. Cache service
6. HTTP Client
7. Command line for non-web tasks
8. Database service
9. Localization service
10. Logger service
11. Mail service (PHPMailer)
12. Paginator service
13. SSH service
14. HTTP Server service
15. Session service
16. No imposition on templating library

OOP, Composer and phpunit
-------------------------
Duality micro framework is developed using the most recent technologies
as PHP Object Oriented Programming approach, Composer as dependency manager
and phpunit to maintain code quality. All code should consider PSR-2 conventions.

Structures and Services
--------------------------
All code is organized into to categories: structures and services
Structures define data limits and validation
Services are Core/User code that respondes to requests

By convention there are 3 folder:

    ./config - where you should put local app.php configuration file
    ./data - where you should save user data
        ./schema.php - where you should put database schema changes
    ./src - where you should put application code

In the demo, you will find a cmd.php that runs on the console like:

    php cmd.php db:seed

App DI Container
----------------
App is an high level DI container that gives flexibility to the user to register their services.
It also can provide a variety of common services used in web development, as:
auth, cache, http client, db, i18n, mailer, paginator, http server and session

Usage example:
```
    $app->register('service', function() {
        return new MyService();
    }, $cacheable = true);
    $app->call('service');
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
    $app->call('cache')->set('key', 'value', '');
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
        ->createTableFromEntity(new User())
        ->find(0, 10)
        ->toArray();
```

Localization Service
--------------------
Example:
```
    $app->call('i18n')
        ->setLocale('pt')
        ->translate('key');
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
        ->set('__lastError', 'Invalid input');
```

No Templating Library
---------------------
Its up to you!

Future (Roadmap)
----------------
Depends on you... ;)
