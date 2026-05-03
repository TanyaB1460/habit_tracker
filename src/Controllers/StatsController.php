<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Models\Habit;

final class StatsController
{
    #[Route(path: '/stats', methods: ['GET'])]
    public function index(): void
    {
        $stats = Habit::getStatsSummary();
        $dailyStats = Habit::getDailyCompletionStats(14);

        require dirname(__DIR__, 2) . '/views/stats/index.php';
    }
}