<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Models\Habit;

final class HabitController
{
    #[Route(path: '/habits', methods: ['GET'])]
    public function index(): void
    {
        $habits = Habit::getAll();

        require dirname(__DIR__, 2) . '/views/habits/index.php';
    }

    #[Route(path: '/habits/create', methods: ['POST'])]
    public function create(): void
    {
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $frequency   = trim($_POST['frequency'] ?? 'daily');

        if ($name !== '') {
            Habit::create(
                $name,
                $description !== '' ? $description : null,
                $frequency
            );
        }

        header('Location: /habits', true, 303);
        exit;
    }

    #[Route(path: '/habits/edit', methods: ['GET'])]
    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/views/errors/404.php';
            return;
        }

        $habit = Habit::findById($id);

        if ($habit === null) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/views/errors/404.php';
            return;
        }

        require dirname(__DIR__, 2) . '/views/habits/edit.php';
    }

    #[Route(path: '/habits/update', methods: ['POST'])]
    public function update(): void
    {
        $id          = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $frequency   = trim($_POST['frequency'] ?? 'daily');

        if ($id > 0 && $name !== '') {
            Habit::update(
                $id,
                $name,
                $description !== '' ? $description : null,
                $frequency
            );
        }

        header('Location: /habits', true, 303);
        exit;
    }


    #[Route(path: '/habits/delete', methods: ['POST'])]
    public function delete(): void
    {
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id > 0) {
            Habit::delete($id);
        }

        header('Location: /habits', true, 303);
        exit;
    }
}
