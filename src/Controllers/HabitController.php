<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Response;
use App\Models\Habit;
use App\Core\Attributes\Route;
final class HabitController
{
    public function __construct(
        private ?object $habitValidator = null,
        private ?object $habitManager = null
    ) {
    }

    #[Route(path: '/habits', methods: ['GET'])]
    public function index(): void
    {
        $habits = Habit::getAll();

        require dirname(__DIR__, 2) . '/views/habits/index.php';
    }

    public function createAction(string $name, ?string $description, string $frequency): Response
    {
        $errors = [];

        if ($this->habitValidator !== null) {
            $errors = $this->habitValidator->validate([
                'name' => $name,
                'description' => $description,
                'frequency' => $frequency,
            ]);
        }

        if ($errors !== []) {
            throw new ValidationException($errors, 'Ошибка валидации');
        }

        if ($this->habitManager !== null) {
            $this->habitManager->create($name, $description, $frequency);
        } else {
            Habit::create($name, $description, $frequency);
        }

        return Response::redirect('/habits');
    }

    #[Route(path: '/habits/create', methods: ['POST'])]
    public function create(): void
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $frequency = trim($_POST['frequency'] ?? 'daily');

        $response = $this->createAction(
            $name,
            $description !== '' ? $description : null,
            $frequency
        );

        foreach ($response->headers as $name => $value) {
            header($name . ': ' . $value, true, $response->statusCode);
        }

        http_response_code($response->statusCode);
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
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $frequency = trim($_POST['frequency'] ?? 'daily');

        if ($id > 0 && $name !== '') {
            Habit::update(
                $id,
                $name,
                $description !== '' ? $description : null,
                $frequency
            );

            header('Location: /habits', true, 303);
            exit;
        }
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