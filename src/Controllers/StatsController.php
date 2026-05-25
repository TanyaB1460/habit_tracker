<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Attributes\Route;
use App\Core\Controller;
use App\Models\Habit;
use Psr\Http\Message\ResponseInterface;

final class StatsController extends Controller
{
    #[Route(path: '/stats', methods: ['GET'])]
    public function index(): ResponseInterface
    {
        $summary = Habit::getStatsSummary();
        $dailyStats = Habit::getDailyCompletionStats();

        $firstStat = $dailyStats[0] ?? null;
        $lastStat = !empty($dailyStats) ? $dailyStats[array_key_last($dailyStats)] : null;

        $periodStart = is_array($lastStat) ? ($lastStat['completed_on'] ?? null) : null;
        $periodEnd = is_array($firstStat) ? ($firstStat['completed_on'] ?? null) : null;

        return $this->render('stats/index', [
            'summary' => $summary,
            'dailyStats' => $dailyStats,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ]);
    }
}