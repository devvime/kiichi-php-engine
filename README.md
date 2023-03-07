# Kiichi PHP Engine

Simple Package PHP for developing web API`s with Kiichi PHP.

***

### Specifications and Dependencies

- **PHP Version** >= 8.1.0
- [Composer](https://getcomposer.org/)
- [PHPMailer](https://github.com/PHPMailer/PHPMailer)
- [Eloquent ORM](https://laravel-docs-pt-br.readthedocs.io/en/latest/eloquent/)
- [RainTPL 3](https://github.com/feulf/raintpl3)
- [php-jwt](https://github.com/firebase/php-jwt)
- [phpdotenv](https://github.com/vlucas/phpdotenv)

#### install package

Run the composer command in project root:

```
composer require devvime/kiichi-php-engine
```

***

#### Configuration

```php
<?php

require_once(__DIR__.'vendor/autoload.php');

use Devvime\KiichiPhpEngine\Application;

$app = new Application();
```

#### Creating routes

Route and Function

```php
$app->get('/', function($req, $res) {
    $res->json(['title'=>'Simple CRUD PHP']);
});
```

#### Route and Class

Folder structure for using classes

```
├── App
|  ├── Controllers
│  |  └── ProductController.php
|  |── Models
│  |  └── Product.php
|  |── Middlewares
│  |  └── ExempleMiddleware.php
```

Class name must contain the word Controller, for example: UserController.php


```php
$app->get('/:id', 'UserController@find');
```

Group of routes and parameters in URL

```php
$app->group('/hello', function() use($app) {
    $app->get('/:name', function($req, $res) {
        $res->render('html-file-name', [
            "name"=>$req->params->name            
        ]);
    });
});

$app->group('/user', function() use($app) {
    $app->get('', 'UserController@index');
    $app->get('/:id', 'UserController@find');
    $app->post('', 'UserController@store');
    $app->put('/:id', 'UserController@update');
    $app->delete('/:id', 'UserController@destroy');
});
```

#### Route and Middleware

Middleware Class name must contain the word Middleware, for example: AuthMiddleware.php

```php

// Middleware in  Function
$app->get('/:id', 'UserController@find', function() {
    // Middleware code...
});

// Middleware in Class
$app->get('/:id', 'UserController@find', 'UserMiddleware@verifyAuthToken');

// Middleware Function in Route Group

$app->group('/user', function() use($app) {
    $app->get('', 'UserController@index');
    $app->get('/:id', 'UserController@find');
    $app->post('', 'UserController@store');
    $app->put('/:id', 'UserController@update');
    $app->delete('/:id', 'UserController@destroy');
}, function() {
    // Middleware code...
});

// Middleware Class in Route Group

$app->group('/user', function() use($app) {
    $app->get('', 'UserController@index');
    $app->get('/:id', 'UserController@find');
    $app->post('', 'UserController@store');
    $app->put('/:id', 'UserController@update');
    $app->delete('/:id', 'UserController@destroy');
}, 'AuthMiddleware@verifyToken');
```

#### Request data

Request data in URL Query ex: http://api.server.com/user?name=steve

```php
$app->post('/user', function($req, $res) {
    $name = $req->query->name;
});
```

Request post data JSON 

```php
$app->post('/user', function($req, $res) {
    $name = $req->body->name;
    $email = $req->body->email;
});
```

Request params in URL

```php
$app->put('/:id', function($req, $res) {
    $id = $req->params->id;
});
```

Start routes

```php
$app->run();
```