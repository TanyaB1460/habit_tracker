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
        $controller = new class {
            #[Route(path: '/', methods: ['GET'])]
            public function index(): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/plain'], 'home page');
            }
        };

        $router = new Router();
        $router->register([get_class($controller)]);

        $response = $router->dispatch(new ServerRequest('GET', '/'));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('home page', (string) $response->getBody());
    }

    public function testDispatchPassesRequestIntoControllerAction(): void
    {
        $controller = new class {
            #[Route(path: '/check-request', methods: ['GET'])]
            public function show(ServerRequestInterface $request): ResponseInterface
            {
                $body = $request->getMethod() . ' ' . $request->getUri()->getPath();

                return new Response(200, ['Content-Type' => 'text/plain'], $body);
            }
        };

        $router = new Router();
        $router->register([get_class($controller)]);

        $response = $router->dispatch(new ServerRequest('GET', '/check-request'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('GET /check-request', (string) $response->getBody());
    }

    public function testDispatchReturns404ForUnknownRoute(): void
    {
        $controller = new class {
            #[Route(path: '/', methods: ['GET'])]
            public function index(): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/plain'], 'home page');
            }
        };

        $router = new Router();
        $router->register([get_class($controller)]);

        $response = $router->dispatch(new ServerRequest('GET', '/missing-page'));

        $this->assertSame(404, $response->getStatusCode());
        $this->assertStringContainsString('404', (string) $response->getBody());
    }

    public function testDispatchNormalizesTrailingSlash(): void
    {
        $controller = new class {
            #[Route(path: '/habits', methods: ['GET'])]
            public function index(): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/plain'], 'habits page');
            }
        };

        $router = new Router();
        $router->register([get_class($controller)]);

        $response = $router->dispatch(new ServerRequest('GET', '/habits/'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('habits page', (string) $response->getBody());
    }

    public function testDispatchReturns404WhenMethodDoesNotMatch(): void
    {
        $controller = new class {
            #[Route(path: '/', methods: ['GET'])]
            public function index(): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/plain'], 'home page');
            }
        };

        $router = new Router();
        $router->register([get_class($controller)]);

        $response = $router->dispatch(new ServerRequest('POST', '/'));

        $this->assertSame(404, $response->getStatusCode());
    }
}
