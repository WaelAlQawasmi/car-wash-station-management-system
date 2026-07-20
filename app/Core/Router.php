<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = '#^' . str_replace(['/', '{id}'], ['\\/', '([0-9]+)'], $route['path']) . '$#';
            if (preg_match($pattern, $path, $matches)) {
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    $handler();
                    return;
                }

                [$controllerName, $methodName] = $handler;
                $controller = new $controllerName();
                $controller->$methodName(...array_slice($matches, 1));
                return;
            }
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
