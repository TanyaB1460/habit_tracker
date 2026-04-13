<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method'     => strtoupper($method),
            'path'       => $path,
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path   = '/' . trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $controller = new $route['controller']();
                $controller->{$route['action']}();
                return;
            }
        }

        http_response_code(404);
        require dirname(__DIR__, 2) . '/views/errors/404.php';
    }

    public function getPost(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function getGet(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
}
