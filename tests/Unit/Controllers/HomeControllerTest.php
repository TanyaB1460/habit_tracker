<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Controllers\HomeController;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

final class HomeControllerTest extends TestCase
{
    public function testIndexReturnsHtmlResponse(): void
    {
        $controller = new HomeController();
        $request = new ServerRequest('GET', '/');

        $response = $controller->index($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/html', $response->getHeaderLine('Content-Type'));
    }
}