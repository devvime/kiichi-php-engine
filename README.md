# Kiichi PHP Engine

Simple Package PHP for developing web API`s with Kiichi PHP.

***

### Specifications and Dependencies

- **PHP Version** >= 8.2.0
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

use Devvime\Kiichi\Engine\Router;

$router = new Router();
```

#### Creating routes

Route and Function

```php
$router->get('/', function($req, $res) {
    $res->json(['title'=>'Simple CRUD PHP']);
});
```

#### Route and Class

Folder structure for using classes

```
├── src
|  ├── Controllers
│  |  └── ProductController.php
|  |── Models
│  |  └── Product.php
|  |── Middlewares
│  |  └── ExempleMiddleware.php
```

Class name must contain the word Controller, for example: UserController.php


```php
$router->get('/:id', 'UserController@find');
```

Group of routes and parameters in URL

```php
$router->group('/hello', function() use($router) {
    $router->get('/:name', function($req, $res) {
        $res->render('html-file-name', [
            "name"=>$req->params->name            
        ]);
    });
});

$router->group('/user', function() use($router) {
    $router->get('', 'UserController@index');
    $router->get('/:id', 'UserController@find');
    $router->post('', 'UserController@store');
    $router->put('/:id', 'UserController@update');
    $router->delete('/:id', 'UserController@destroy');
});
```

#### Route and Middleware

Middleware Class name must contain the word Middleware, for example: AuthMiddleware.php

```php

// Middleware in  Function
$router->get('/:id', 'UserController@find', function() {
    // Middleware code...
});

// Middleware in Class
$router->get('/:id', 'UserController@find', 'UserMiddleware@verifyAuthToken');

// Middleware Function in Route Group

$router->group('/user', function() use($router) {
    $router->get('', 'UserController@index');
    $router->get('/:id', 'UserController@find');
    $router->post('', 'UserController@store');
    $router->put('/:id', 'UserController@update');
    $router->delete('/:id', 'UserController@destroy');
}, function() {
    // Middleware code...
});

// Middleware Class in Route Group

$router->group('/user', function() use($router) {
    $router->get('', 'UserController@index');
    $router->get('/:id', 'UserController@find');
    $router->post('', 'UserController@store');
    $router->put('/:id', 'UserController@update');
    $router->delete('/:id', 'UserController@destroy');
}, 'AuthMiddleware@verifyToken');
```

#### Request data

Request data in URL Query ex: http://api.server.com/user?name=steve

```php
$router->post('/user', function($req, $res) {
    $name = $req->query->name;
});
```

Request post data JSON 

```php
$router->post('/user', function($req, $res) {
    $name = $req->body->name;
    $email = $req->body->email;
});
```

Request params in URL

```php
$router->put('/:id', function($req, $res) {
    $id = $req->params->id;
});
```

Start routes

```php
$router->run();
```

#### Render HTML file

To render an HTML file just use $res->render('file-name');
no need to add .html in file name

```php
$router->get('/user', function($req, $res) use($router) {
    $res->render('html-file-name');
});
```

To render an HTML file by sending an array of data use $res->render('file-name');

```php
$router->get('/user', function($req, $res) use($router) {
    $res->render('html-file-name', [
        "name"=>$user->name,
        "email"=>$user->email,
        "product"=>$productArray
    ]);
});
```

To receive the data sent to the HTML file use {{ key }} or {{ key.object.name }}

```html
<div class="card">
    <h4>{{ name }}</h4>
    <p>{{ email }}</p>
    <hr/>
    <p>{{ product.title }}</p>
    <p>{{ product.description }}</p>
    <p>{{ product.price }}</p>
</div>
```

For more details, see the documentation at [RainTPL 3](https://github.com/feulf/raintpl3)