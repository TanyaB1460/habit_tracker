<?php

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
        extract($data);
        require dirname(__DIR__, 2) . '/views/' . $view . '.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}