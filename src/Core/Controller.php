<?php

declare(strict_types=1);

namespace App\Core;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    //Рендер HTML-шаблона в PSR-7 Response
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

    //Создать редирект с заголовком Location.
    protected function redirect(string $path, int $statusCode = 303): ResponseInterface
    {
        return new Response(
            $statusCode,
            ['Location' => $path],
            ''
        );
    }

    //Гарантировать строку, иначе вернуть значение по умолчанию.
    protected function ensureString(mixed $value, string $default = ''): string
    {
        return is_string($value) ? $value : $default;
    }

    //Преобразовать пустую строку в null
    protected function emptyToNull(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value === '' ? null : $value;
    }

    //Прочитать и валидировать целое число из POST
    protected function postInt(string $key): ?int
    {
        $value = $_POST[$key] ?? null;

        if ($value === null) {
            return null;
        }

        $validated = filter_var($value, FILTER_VALIDATE_INT);

        return $validated === false ? null : $validated;
    }

    //Прочитать и валидировать целое число из GET
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
