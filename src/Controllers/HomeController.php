<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Attributes\Route;
use App\Models\Habit;

final class HomeController extends Controller
{
    #[Route(path: '/', methods: ['GET'])]
    public function index(): void
    {
        $today = date('Y-m-d');
        $habits = Habit::getAllWithStatusForDate($today);

        $this->render('home', [
            'today' => $today,
            'habits' => $habits,
        ]);
    }
}
