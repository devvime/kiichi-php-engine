<?php

namespace Devvime\Kiichi\Engine;

use Devvime\Kiichi\Engine\HttpService;
use Devvime\Kiichi\Engine\ControllerService;

class Router {

    public $path;
    public $http;
    public $params = [];
    public $req;
    public $res;
    public $routes = [];
    public $groupRoutes = [];
    public $group;
    public $middleware;

    public function __construct($group = "")
    {
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->http = $_SERVER['REQUEST_METHOD'];
        $this->group = $group;
    }

    public function getController($controller) 
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../src/Controllers/{$controller}.php")) {
            require_once($_SERVER['DOCUMENT_ROOT'] . "/../src/Controllers/{$controller}.php");
            $class = "Devvime\\Kiichi\\Controllers\\". $controller;
            return new $class();
        } else {
            echo json_encode([
                "error"=>404,
                "message"=>"src/Controllers/{$controller}.php is not found!"
            ]);
            exit;
        }
    }

    public function getMiddleware($middleware) 
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/../src/Middlewares/{$middleware}.php")) {
            require_once($_SERVER['DOCUMENT_ROOT'] . "/../src/Middlewares/{$middleware}.php");
            $class = "Devvime\\Kiichi\\Middlewares\\". $middleware;
            return new $class();
        } else {
            echo json_encode([
                "error"=>404,
                "message"=>"src/Middlewares/" . $middleware . ".php is not found!"
            ]);
            exit;
        }
    }

    public function getParams($route, $method)
    {
        $this->req = new \stdClass;
        @$this->req->body = HttpService::request();
        @$this->req->query = json_decode(json_encode($_GET));
        $this->res = new ControllerService();
        if (strpos($route, ":") && $this->http === $method) {
            $pathArray = explode('/', $this->path);
            $routeArray = explode('/', $route);
            for ($i=0; $i < count($routeArray); $i++) {
                if (strpos($routeArray[$i], ":") !== false) {  
                    if (isset($routeArray[$i]) && isset($pathArray[$i])) {                        
                        $this->params[str_replace(":", '', $routeArray[$i])] = $pathArray[$i];
                        $routeArray[$i] = $pathArray[$i];
                    }                                  
                }
            }
            $objParams = json_encode($this->params);
            @$this->req->params = json_decode($objParams);
            return implode('/', $routeArray);
        } else {
            return $route;
        }
    }

    public function verify($route, $controller, $method, $middleware)
    {
        if ($middleware !== null) {
            $this->middleware($middleware);
        }
        if ($this->getParams($route, $method) === $this->path && $this->http === $method && is_string($controller)) {
            $controller = explode('@', $controller);
            $class = $this->getController($controller[0]);
            $callback = $controller[1];           
            $class->$callback($this->req, $this->res);            
            exit;
        } else if ($this->getParams($route, $method) === $this->path && $this->http === $method && !is_string($controller)) {
            $callback = $controller;     
            $callback($this->req, $this->res);
            exit;
        }
        $this->routes[] = $route;
    }

    public function get($route, $controller, $middleware = null)    
    {
        $this->verify($this->group . $route, $controller, 'GET', $middleware);
    }

    public function post($route, $controller, $middleware = null)
    {
        $this->verify($this->group . $route, $controller, 'POST', $middleware);
    }

    public function put($route, $controller, $middleware = null)
    {
        $this->verify($this->group . $route, $controller, 'PUT', $middleware);
    }

    public function delete($route, $controller, $middleware = null)
    {
        $this->verify($this->group . $route, $controller, 'DELETE', $middleware);
    }

    public function group($name, $function, $middleware = null)
    {
        $previousGroup = $this->group; 
        $this->group = $name;

        if (strpos($this->path, $this->group) === 0) {            
            if ($middleware !== null) {
                $this->middleware($middleware);
            }
            $callback = $function;     
            $callback($this->req, $this->res);
        }

        $this->group = $previousGroup; // restore previous group after group call
    }

    public function middleware($callback)
    {
        $this->middleware = new \stdClass;
        $this->middleware->callback = $callback;
        if (!is_string($this->middleware->callback)) {
            $callback = $this->middleware->callback;
            $middleware = $callback($this->req, $this->res);
        } else if (is_string($this->middleware->callback)) {
            $middleware = explode('@', $this->middleware->callback);
            $class = $this->getMiddleware($middleware[0]);
            $callback = $middleware[1];           
            $middleware = $class->$callback($this->req, $this->res);
        }
    }

    public function next()
    {
        $this->middleware->callback = null;
    }

    public function run()
    {
        foreach ($this->routes as $route) {
            if ($this->getParams($route, $this->http) === $this->path) {
                return; // route found, so return without error
            }
        }

        echo json_encode([
            "status"=>404,
            "message"=>"Error: Endpoint is not found!",
            "path"=>$this->path,
            "method"=>$this->http
        ]);
        exit;
    }

}