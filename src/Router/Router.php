<?php

namespace SAC_WebAPI\Router;

use SAC_WebAPI\Controllers\Controller;

include_once './Controllers/Controller.php';

class Router{
    private $routes = [];

    public function on($method, $path, $callback){
        $method = strtolower($method);
        if(!isset($this->routes[$method])){ // Se essa é a primeira rota para esse método
            $this->routes[$method] = [];
        }

        // Atrela a função passada como parâmetro a um método e uma rota
        $uri = substr($path, 0, 1) !== '/' ? '/'. $path : $path;
        $pattern = str_replace('/', '\/', $uri);
        $route = '/^' . $pattern . '$/';
        $this->routes[$method][$route] = $callback;

        return $this;
    }

    function run($method, $uri){
        $method = strtolower($method);
        if(!isset($this->routes[$method])){ // Caso não existam rotas para esse método
            http_response_code(400);    
            return null;
        }

        foreach($this->routes[$method] as $route => $callback){
            if(preg_match($route, $uri, $parameters)){
                array_shift($parameters); // Exclui o match total e deixa só os parâmetros
                return call_user_func_array($callback, $parameters);
            }
        }

        http_response_code(400);    
        return null; // Caso essa rota não exista
    }
}

?>