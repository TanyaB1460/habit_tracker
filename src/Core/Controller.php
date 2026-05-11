<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . '/views/' . $view . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path, true, 303);
        exit;
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