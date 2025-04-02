<?php
namespace Core;

class Router {
    protected $routes = [];

    public function add($route, $params = []) {
        $this->routes[$route] = $params;
    }

    public function dispatch($url) {
        $url = $this->removeQueryStringVariables($url);
        
        foreach ($this->routes as $route => $params) {
            if (preg_match($this->convertToRegex($route), $url, $matches)) {
                $controller = $params['controller'] ?? 'Home';
                $action = $params['action'] ?? 'index';
                
                $controller = "App\Controllers\\" . ucfirst($controller) . 'Controller';
                if (class_exists($controller)) {
                    $controller_object = new $controller();
                    call_user_func_array([$controller_object, $action], []);
                    return;
                }
            }
        }
        
        http_response_code(404);
        require __DIR__ . "/../../public/404.php";
    }

    protected function convertToRegex($route) {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        return '/^' . $route . '$/i';
    }

    protected function removeQueryStringVariables($url) {
        return preg_replace('/([\?&].*)/', '', $url);
    }
}