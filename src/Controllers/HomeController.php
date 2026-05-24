<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\ValidationException;
use App\Http\Response;
use App\Models\Habit;
use App\Core\Attributes\Route;
final class HomeController
{
    public function __construct(
        private ?object $habitManager = null
    ) {
    }

    #[Route(path: '/', methods: ['GET'])]
    public function index(): void
    {
        $today = date('Y-m-d');
        $habits = Habit::getAllWithStatusForDate($today);

        require dirname(__DIR__, 2) . '/views/home.php';
    }

    public function toggleAction(int $habitId, string $date): Response
    {
        if ($habitId <= 0) {
            throw new ValidationException([
                'habit_id' => 'Некорректный идентификатор привычки.',
            ], 'Ошибка валидации');
        }

        if ($this->habitManager !== null) {
            $this->habitManager->toggleForDate($habitId, $date);
        } else {
            Habit::toggleForDate($habitId, $date);
        }

        return Response::redirect('/');
    }

    #[Route(path: '/toggle', methods: ['POST'])]
    public function toggle(): void
    {
        $habitId = isset($_POST['habit_id']) ? (int) $_POST['habit_id'] : 0;
        $date = $_POST['date'] ?? date('Y-m-d');

        $response = $this->toggleAction($habitId, $date);

        foreach ($response->headers as $name => $value) {
            header($name . ': ' . $value, true, $response->statusCode);
        }

        http_response_code($response->statusCode);
        exit;
    }
}