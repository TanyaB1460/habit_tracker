<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Middleware;

use App\Core\Middleware\LoggerMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

final class LoggerMiddlewareTest extends TestCase
{
    public function testProcessLogsRequestAndResponse(): void
    {
        $logger = $this->createLogger();
        $middleware = new LoggerMiddleware($logger);

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'text/plain'], 'ok');
            }
        };

        $response = $middleware->process(new ServerRequest('GET', '/habits'), $handler);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(2, $logger->records);
        $this->assertSame('Request received', $logger->records[0]['message']);
        $this->assertSame('GET', $logger->records[0]['context']['method']);
        $this->assertStringContainsString('/habits', $logger->records[0]['context']['uri']);
        $this->assertSame('Response sent', $logger->records[1]['message']);
        $this->assertSame(200, $logger->records[1]['context']['status_code']);
    }

    public function testProcessPassesThroughHandlerResponse(): void
    {
        $logger = $this->createLogger();
        $middleware = new LoggerMiddleware($logger);

        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(404, [], 'not found');
            }
        };

        $response = $middleware->process(new ServerRequest('GET', '/nope'), $handler);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(404, $logger->records[1]['context']['status_code']);
    }

    private function createLogger(): LoggerInterface
    {
        return new class implements LoggerInterface {
            public array $records = [];

            public function info(\Stringable|string $message, array $context = []): void
            {
                $this->records[] = [
                    'level' => 'info',
                    'message' => (string) $message,
                    'context' => $context,
                ];
            }

            public function emergency(\Stringable|string $message, array $context = []): void
            {
                $this->log('emergency', $message, $context);
            }

            public function alert(\Stringable|string $message, array $context = []): void
            {
                $this->log('alert', $message, $context);
            }

            public function critical(\Stringable|string $message, array $context = []): void
            {
                $this->log('critical', $message, $context);
            }

            public function error(\Stringable|string $message, array $context = []): void
            {
                $this->log('error', $message, $context);
            }

            public function warning(\Stringable|string $message, array $context = []): void
            {
                $this->log('warning', $message, $context);
            }

            public function notice(\Stringable|string $message, array $context = []): void
            {
                $this->log('notice', $message, $context);
            }

            public function debug(\Stringable|string $message, array $context = []): void
            {
                $this->log('debug', $message, $context);
            }

            public function log($level, \Stringable|string $message, array $context = []): void
            {
                $this->records[] = [
                    'level' => (string) $level,
                    'message' => (string) $message,
                    'context' => $context,
                ];
            }
        };
    }
}
