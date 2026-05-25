<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Controller;
use App\Exceptions\ValidationException;
use App\Repositories\HabitRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HomeController extends Controller
{
    private HabitRepository $habitRepository;

    public function __construct(?HabitRepository $habitRepository = null)
    {
        $this->habitRepository = $habitRepository ?? new HabitRepository();
    }

    #[Route(path: '/', methods: ['GET'])]
    public function index(): ResponseInterface
    {
        $today = date('Y-m-d');
        $habits = $this->habitRepository->getAllWithStatusForDate($today);

        return $this->render('home', [
            'today' => $today,
            'habits' => $habits,
        ]);
    }

    public function toggleAction(int $habitId, string $date): ResponseInterface
    {
        if ($habitId <= 0) {
            throw new ValidationException([
                'habit_id' => 'Некорректный идентификатор привычки.',
            ], 'Ошибка валидации');
        }

        $this->habitRepository->toggleForDate($habitId, $date);

        return $this->redirect('/');
    }

    #[Route(path: '/toggle', methods: ['POST'])]
    public function toggle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        if (!is_array($data)) {
            $data = [];
        }

        $habitId = isset($data['habit_id']) ? (int) $data['habit_id'] : 0;
        $date = is_string($data['date'] ?? null) ? $data['date'] : date('Y-m-d');

        return $this->toggleAction($habitId, $date);
    }
}