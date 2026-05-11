<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Habit;
use App\Core\Attributes\Route;
use App\Exceptions\ValidationException;

final class HabitController extends Controller
{
    #[Route(path: '/habits', methods: ['GET'])]
    public function index(): void
    {
        $habits = Habit::getAll();

        $this->render('habits/index', [
            'habits' => $habits,
        ]);
    }

    #[Route(path: '/habits/create', methods: ['POST'])]
    public function create(): void
    {
        $name = ($_POST['name'] ?? '')
                |> $this->ensureString(...)
                |> trim(...);

        $description = ($_POST['description'] ?? '')
                |> $this->ensureString(...)
                |> trim(...)
                |> $this->emptyToNull(...);

        $frequency = ($_POST['frequency'] ?? 'daily')
                |> $this->ensureString(...)
                |> trim(...);

        if ($name === '') {
            throw new ValidationException('Название привычки не может быть пустым.');
        }

        Habit::create($name, $description, $frequency);

        $this->redirect('/habits');
    }

    #[Route(path: '/habits/edit', methods: ['GET'])]
    public function edit(): void
    {
        $id = $this->getInt('id');

        if ($id === null || $id <= 0) {
            http_response_code(404);
            require dirname(__DIR__) . '/views/errors/404.php';
            return;
        }

        $habit = Habit::findById($id);

        if ($habit === null) {
            http_response_code(404);
            require dirname(__DIR__) . '/views/errors/404.php';
            return;
        }

        $this->render('habits/edit', [
            'habit' => $habit,
        ]);
    }

    #[Route(path: '/habits/update', methods: ['POST'])]
    public function update(): void
    {
        $id = $this->postInt('id');

        $name = ($_POST['name'] ?? '')
                |> $this->ensureString(...)
                |> trim(...);

        $description = ($_POST['description'] ?? '')
                |> $this->ensureString(...)
                |> trim(...)
                |> $this->emptyToNull(...);

        $frequency = ($_POST['frequency'] ?? 'daily')
                |> $this->ensureString(...)
                |> trim(...);

        if ($id === null || $id <= 0) {
            throw new ValidationException('Некорректный идентификатор привычки.');
        }

        if ($name === '') {
            throw new ValidationException('Название привычки не может быть пустым.');
        }

        Habit::update($id, $name, $description, $frequency);

        $this->redirect('/habits');
    }

    #[Route(path: '/habits/delete', methods: ['POST'])]
    public function delete(): void
    {
        $id = $this->postInt('id');

        if ($id === null || $id <= 0) {
            throw new ValidationException('Некорректный идентификатор привычки.');
        }

        Habit::delete($id);

        $this->redirect('/habits');
    }

    #[Route(path: '/habits/toggle', methods: ['POST'])]
    public function toggle(): void
    {
        $habitId = $this->postInt('habit_id');

        $date = ($_POST['date'] ?? date('Y-m-d'))
                |> $this->ensureString(...)
                |> trim(...);

        if ($habitId === null || $habitId <= 0) {
            throw new ValidationException('Некорректный идентификатор привычки.');
        }

        Habit::toggleForDate($habitId, $date);

        $this->redirect('/');
    }
}
