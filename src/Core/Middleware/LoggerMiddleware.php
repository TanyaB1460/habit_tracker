<?php

namespace App\Core\Middleware;

use Psr\Log\LoggerInterface;

class LoggerMiddleware
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(string $method, string $uri, callable $next): void
    {
        $this->logger->info("Request received", [
            'method' => $method,
            'uri'    => $uri
        ]);

        $next($method, $uri);
    }
}
