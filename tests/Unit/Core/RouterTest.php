<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use App\Core\Attributes\Route;
use App\Core\Router;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RouterTest extends TestCase
{
    public function testDispatchReturnsControllerResponseForRegisteredRoute(): void
    {
        $router = new Router();
        $router->register([
            TestHomeController::class,
        ]);

        $request = new ServerRequest('GET', '/');
        $response = $router->dispatch($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('home page', (string) $response->getBody());
    }

    public function testDispatchPassesRequestIntoControllerAction(): void
    {
        $router = new Router();
        $router->register([
            TestRequestAwareController::class,
        ]);

        $request = new ServerRequest('GET', '/check-request');
        $response = $router->dispatch($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('GET /check-request', (string) $response->getBody());
    }

    public function testDispatchReturns404ForUnknownRoute(): void
    {
        $router = new Router();
        $router->register([
            TestHomeController::class,
        ]);

        $request = new ServerRequest('GET', '/missing-page');
        $response = $router->dispatch($request);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('404', (string) $response->getBody());
    }

    public function testDispatchNormalizesTrailingSlash(): void
    {
        $router = new Router();
        $router->register([
            TestHabitsController::class,
        ]);

        $request = new ServerRequest('GET', '/habits/');
        $response = $router->dispatch($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('habits page', (string) $response->getBody());
    }
}

final class TestHomeController
{
    #[Route(path: '/', methods: ['GET'])]
    public function index(): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'text/plain; charset=UTF-8'], 'home page');
    }
}

final class TestHabitsController
{
    #[Route(path: '/habits', methods: ['GET'])]
    public function index(): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'text/plain; charset=UTF-8'], 'habits page');
    }
}

final class TestRequestAwareController
{
    #[Route(path: '/check-request', methods: ['GET'])]
    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getMethod() . ' ' . $request->getUri()->getPath();

        return new Response(200, ['Content-Type' => 'text/plain; charset=UTF-8'], $body);
    }
}
