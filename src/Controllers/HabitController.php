<?php

namespace App\Controllers;

use App\Core\Controller;

class HabitController extends Controller
{
    // Список всех привычек + форма добавления
    public function index(): void
    {
        $this->render('habits/index');
    }

    // Добавить привычку (POST)
    public function create(): void
    {
        $name = $this->router->getPost('name', '');
        $goal = $this->router->getPost('goal', '');
        // TODO: сохранить привычку
        $this->redirect('/habits');
    }

    // Форма редактирования
    public function edit(): void
    {
        $id = (int) $this->router->getGet('id');
        // TODO: загрузить привычку по $id
        $this->render('habits/edit');
    }

    // Сохранить изменения (POST)
    public function update(): void
    {
        $id   = (int) $this->router->getPost('id');
        $name = $this->router->getPost('name', '');
        $goal = $this->router->getPost('goal', '');
        // TODO: обновить привычку
        $this->redirect('/habits');
    }

    // Удалить привычку (POST)
    public function delete(): void
    {
        $id = (int) $this->router->getPost('id');
        // TODO: удалить привычку
        $this->redirect('/habits');
    }
}
