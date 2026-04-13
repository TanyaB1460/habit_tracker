<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    // Главная страница — список привычек на сегодня
    public function index(): void
    {
        $this->render('home');
    }

    // Отметить привычку выполненной за сегодня
    public function toggle(): void
    {
        $id = (int) $this->router->getPost('id');
        // TODO: отметить выполнение
        $this->redirect('/');
    }
}
