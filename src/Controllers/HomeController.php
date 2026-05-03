<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Models\Habit;

final class HomeController
{
    #[Route(path: '/', methods: ['GET'])]
    public function index(): void
    {
        $today = date('Y-m-d');
        $habits = Habit::getAllWithStatusForDate($today);

        require dirname(__DIR__, 2) . '/views/home.php';
    }

    #[Route(path: '/toggle', methods: ['POST'])]
    public function toggle(): void
    {
        $habitId = isset($_POST['habit_id']) ? (int) $_POST['habit_id'] : 0;
        $date = $_POST['date'] ?? date('Y-m-d');

        if ($habitId > 0) {
            Habit::toggleForDate($habitId, $date);
        }

        header('Location: /', true, 303);
        exit;
    }
}