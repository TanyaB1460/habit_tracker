<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Attributes;

use App\Core\Attributes\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
    public function testRouteStoresPathAndMethods(): void
    {
        $route = new Route('/habits', ['GET', 'POST']);

        $this->assertSame('/habits', $route->path);
        $this->assertSame(['GET', 'POST'], $route->methods);
    }

    public function testRouteCanBeCreatedWithSingleMethod(): void
    {
        $route = new Route('/stats', ['GET']);

        $this->assertSame('/stats', $route->path);
        $this->assertCount(1, $route->methods);
        $this->assertSame('GET', $route->methods[0]);
    }

    #[DataProvider('routePathProvider')]
    public function testRouteStoresVariousPaths(string $path, array $methods): void
    {
        $route = new Route($path, $methods);

        $this->assertSame($path, $route->path);
        $this->assertSame($methods, $route->methods);
    }

    public static function routePathProvider(): array
    {
        return [
            'root path'         => ['/', ['GET']],
            'nested path'       => ['/habits/edit', ['GET']],
            'post route'        => ['/habits/create', ['POST']],
            'delete route'      => ['/habits/delete', ['POST']],
            'multiple methods'  => ['/api/resource', ['GET', 'POST', 'DELETE']],
        ];
    }
}
