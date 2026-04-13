<?php

namespace App\Controllers;

use App\Core\Controller;

class StatsController extends Controller
{
    // Страница статистики — процент выполнения за месяц
    public function index(): void
    {
        $this->render('stats/index');
    }
}
