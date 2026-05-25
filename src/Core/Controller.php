<?php

declare(strict_types=1);

namespace App\Core;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    protected function render(string $view, array $data = [], int $statusCode = 200): ResponseInterface
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require dirname(__DIR__, 2) . '/views/' . $view . '.php';
        $content = (string) ob_get_clean();

        return new Response(
            $statusCode,
            ['Content-Type' => 'text/html; charset=UTF-8'],
            $content
        );
    }

    protected function redirect(string $path, int $statusCode = 303): ResponseInterface
    {
        return new Response(
            $statusCode,
            ['Location' => $path],
            ''
        );
    }

    protected function ensureString(mixed $value, string $default = ''): string
    {
        return is_string($value) ? $value : $default;
    }

    protected function emptyToNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value === '' ? null : $value;
    }

    protected function postInt(string $key): ?int
    {
        $value = $_POST[$key] ?? null;

        if ($value === null) {
            return null;
        }

        $validated = filter_var($value, FILTER_VALIDATE_INT);

        return $validated === false ? null : $validated;
    }

    protected function getInt(string $key): ?int
    {
        $value = $_GET[$key] ?? null;

        if ($value === null) {
            return null;
        }

        $validated = filter_var($value, FILTER_VALIDATE_INT);

        return $validated === false ? null : $validated;
    }
}