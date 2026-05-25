<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Middleware;

use App\Core\Middleware\LoggerMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;

final class LoggerMiddlewareTest extends TestCase
{
    public function testProcessLogsRequestAndResponse(): void
    {
        $logger = new TestLogger();
        $middleware = new LoggerMiddleware($logger);

        $request = new ServerRequest('GET', '/habits');

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/plain'], 'ok');
            }
        };

        $response = $middleware->process($request, $handler);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(2, $logger->records);

        $this->assertSame('Request received', $logger->records[0]['message']);
        $this->assertSame('GET', $logger->records[0]['context']['method']);
        $this->assertSame('/habits', $logger->records[0]['context']['uri']);

        $this->assertSame('Response sent', $logger->records[1]['message']);
        $this->assertSame(200, $logger->records[1]['context']['status_code']);
    }
}

final class TestLogger implements LoggerInterface
{
    public array $records = [];

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'emergency', 'message' => (string) $message, 'context' => $context];
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'alert', 'message' => (string) $message, 'context' => $context];
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'critical', 'message' => (string) $message, 'context' => $context];
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'error', 'message' => (string) $message, 'context' => $context];
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'warning', 'message' => (string) $message, 'context' => $context];
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'notice', 'message' => (string) $message, 'context' => $context];
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'info', 'message' => (string) $message, 'context' => $context];
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => 'debug', 'message' => (string) $message, 'context' => $context];
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
    }
}
