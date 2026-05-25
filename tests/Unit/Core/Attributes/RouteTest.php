<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Attributes;

use App\Core\Attributes\Route;
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
}
