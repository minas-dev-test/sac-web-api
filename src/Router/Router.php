<?php

namespace SAC_WebAPI\Router;

use SAC_WebAPI\Controllers\Controller;

include_once './Controllers/Controller.php';

class Router{
    private $routes = [];
    private $request_data;

    function __construct($request_data){
        $this->request_data = $request_data;            
    }

    public function on($method, $path, $callback){
        $method = strtolower($method);
        if(!isset($this->routes[$method])){
            $this->routes[$method] = [];
        }

        $uri = substr($path, 0, 1) !== '/' ? '/'. $path : $path;
        $pattern = str_replace('/', '\/', $uri);
        $route = '/^' . $pattern . '$/';
        $this->routes[$method][$route] = $callback;

        return $this;
    }

    function run($method, $uri){
        $method = strtolower($method);
        if(!isset($this->routes[$method])){
            return null;
        }

        foreach($this->routes[$method] as $route => $callback){
            if(preg_match($route, $uri, $parameters)){
                array_shift($parameters);
                if($parameters[0] != NULL){
                    return call_user_func_array($callback, $parameters);
                }else{
                    return call_user_func($callback, $this->request_data);
                }
            }
        }

        return null;

    }


}

?>