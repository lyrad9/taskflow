<?php
class Router {
    private $routes = [];

    public function addRoute($route, $controllerAction) {
        // Convertit les routes avec paramètres en motifs regex
        $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>[^/]+)', $route);
        $this->routes[$route] = [
            'pattern' => '#^' . $pattern . '$#',
            'controllerAction' => $controllerAction
        ];
    }

    public function dispatch() {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $uri = explode('?', $uri)[0];
        $found = false;

        foreach ($this->routes as $routeConfig) {
            if (preg_match($routeConfig['pattern'], $uri, $matches)) {
                // Récupère les paramètres dynamiques
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                list($controllerName, $action) = explode('@', $routeConfig['controllerAction']);
                $controllerFile = 'app/controllers/' . $controllerName . '.php';

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $controllerName();
                    $controller->$action(...array_values($params));
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            require_once 'app/views/404.php';
        }
    }
}
?>