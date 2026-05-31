<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Attributes\Route;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

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

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtoupper($request->getMethod());
        $path = $this->normalizePath($request->getUri()->getPath());

        if (!isset($this->routes[$method][$path])) {
            return $this->renderView(
                dirname(__DIR__, 2) . '/views/errors/404.php',
                404
            );
        }

        $route = $this->routes[$method][$path];
        $controllerClass = $route['controller'];
        $action = $route['action'];

        $controller = new $controllerClass();
        $reflectionMethod = new ReflectionMethod($controller, $action);

        $args = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && $type->getName() === ServerRequestInterface::class) {
                $args[] = $request;
            }
        }

        $result = $reflectionMethod->invokeArgs($controller, $args);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    public function renderView(string $viewPath, int $statusCode): ResponseInterface
    {
        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();

        return new Response(
            $statusCode,
            ['Content-Type' => 'text/html; charset=UTF-8'],
            $content
        );
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);

        if ($path === '' || $path === '/') {
            return '/';
        }

        $normalized = '/' . trim($path, '/');

        return $normalized === '' ? '/' : $normalized;
    }
}
