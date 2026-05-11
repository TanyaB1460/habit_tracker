<?php

declare(strict_types=1);

namespace App\Controllers;

final class StatsController extends Controller
{
    #[Route(path: '/stats', methods: ['GET'])]
    public function index(): void
    {
        $summary = Habit::getStatsSummary();
        $dailyStats = Habit::getDailyCompletionStats();

        $firstStat = array_first($dailyStats);
        $lastStat = array_last($dailyStats);

        $periodStart = is_array($lastStat) ? ($lastStat['completed_on'] ?? null) : null;
        $periodEnd = is_array($firstStat) ? ($firstStat['completed_on'] ?? null) : null;

        $this->render('stats/index', [
            'summary' => $summary,
            'dailyStats' => $dailyStats,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ]);
    }
}