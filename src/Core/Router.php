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
            $reflectionClass = new ReflectionClass($controllerClass);

            foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $routeMeta = $attribute->newInstance();
                    $path = $this->normalizePath($routeMeta->path);

                    foreach ($routeMeta->methods as $httpMethod) {
                        $methodName = strtoupper((string) $httpMethod);

                        $this->routes[$methodName][$path] = [
                            'controller' => $controllerClass,
                            'action' => $method->getName(),
                        ];
                    }
                }
            }
        }
    }

    public function dispatch(string $httpMethod, string $uri): void
    {
        $method = strtoupper($httpMethod);
        $path = $this->extractPath($uri);

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/views/errors/404.php';
            return;
        }

        $route = $this->routes[$method][$path];
        $controllerClass = $route['controller'];
        $action = $route['action'];

        $controller = new $controllerClass();
        $controller->$action();
    }

    private function normalizePath(string $path): string
    {
        if ($path === '' || $path === '/') {
            return '/';
        }

        return '/' . ltrim(trim($path), '/');
    }

    private function extractPath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return '/';
        }

        return $this->normalizePath($path);
    }
}
