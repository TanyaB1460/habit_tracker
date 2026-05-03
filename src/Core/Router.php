<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Attributes\Route;
use ReflectionClass;
use ReflectionMethod;

final class Router
{
    private array $routes = [];

    public function register(array $controllerClasses): void
    {
        foreach ($controllerClasses as $controllerClass) {
            $refClass = new ReflectionClass($controllerClass);

            foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $routeMeta = $attribute->newInstance();

                    $path = $this->normalizePath($routeMeta->path);

                    foreach ($routeMeta->methods as $httpMethod) {
                        $httpMethod = strtoupper($httpMethod);

                        $this->routes[$httpMethod][$path] = [
                            'controller' => $controllerClass,
                            'action'     => $method->getName(),
                        ];
                    }
                }
            }
        }
    }


    public function dispatch(string $httpMethod, string $uri): void
    {
        $httpMethod = strtoupper($httpMethod);
        $path       = $this->extractPath($uri);

        if (!isset($this->routes[$httpMethod][$path])) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/views/errors/404.php';
            return;
        }

        $route = $this->routes[$httpMethod][$path];

        $controllerClass = $route['controller'];
        $action          = $route['action'];

        $controller = new $controllerClass();
        $controller->$action();
    }

    private function normalizePath(string $path): string
    {
        if ($path === '' || $path === '/') {
            return '/';
        }

        return '/' . ltrim($path, '/');
    }


    private function extractPath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        return $this->normalizePath($path);
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