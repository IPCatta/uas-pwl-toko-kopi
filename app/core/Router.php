<?php
class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, string $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, string $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();
        $matched = $this->matchRoute($method, $uri);

        if ($matched === null) {
            $this->notFound();
            return;
        }

        [$handler, $params] = $matched;
        $this->callHandler($handler, $params);
    }

    private function getUri(): string {
        $uri = $_SERVER['REQUEST_URI'];
        if (($pos = strpos($uri, '?')) !== false) $uri = substr($uri, 0, $pos);
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && $basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        if ($uri === '' || $uri === false) $uri = '/';
        if ($uri !== '/') $uri = rtrim($uri, '/');
        return $uri;
    }

    private function matchRoute(string $method, string $uri): ?array {
        if (!isset($this->routes[$method])) return null;
        foreach ($this->routes[$method] as $routePath => $handler) {
            $params = $this->matchPath($routePath, $uri);
            if ($params !== false) return [$handler, $params];
        }
        return null;
    }

    private function matchPath(string $routePath, string $uri): array|false {
        if (!str_contains($routePath, '{')) return ($routePath === $uri) ? [] : false;
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        if (count($routeParts) !== count($uriParts)) return false;
        $params = [];
        foreach ($routeParts as $i => $routePart) {
            if (preg_match('/^\{(\w+)\}$/', $routePart, $m)) {
                $params[$m[1]] = $uriParts[$i];
            } else if ($routePart !== $uriParts[$i]) {
                return false;
            }
        }
        return $params;
    }

    private function callHandler(string $handler, array $params): void {
        [$controllerPath, $method] = explode('@', $handler);
        $subFolder = '';
        $className = $controllerPath;
        if (str_contains($controllerPath, '/')) {
            $parts = explode('/', $controllerPath);
            $className = array_pop($parts);
            $subFolder = implode('/', $parts) . '/';
        }
        $controllerFile = APP_PATH . '/controllers/' . $subFolder . $className . '.php';
        if (!file_exists($controllerFile)) { $this->notFound(); return; }
        require_once $controllerFile;
        if (!class_exists($className)) { $this->notFound(); return; }
        $controller = new $className();
        if (!method_exists($controller, $method)) { $this->notFound(); return; }
        call_user_func_array([$controller, $method], $params);
    }

    private function notFound(): void {
        http_response_code(404);
        $errorPage = APP_PATH . '/views/errors/404.php';
        if (file_exists($errorPage)) require $errorPage;
        else echo '<h1>404 Not Found</h1>';
    }
}
