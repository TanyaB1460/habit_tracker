<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Habit;

class HabitRepository
{
    public function getAll(): array
    {
        return Habit::getAll();
    }

    public function findById(int $id): ?array
    {
        return Habit::findById($id);
    }

    public function create(
        string $name,
        ?string $description,
        string $frequency = 'daily',
        ?int $categoryId = null
    ): void {
        Habit::create($name, $description, $frequency, $categoryId);
    }

    public function update(
        int $id,
        string $name,
        ?string $description,
        string $frequency,
        ?int $categoryId = null
    ): void {
        Habit::update($id, $name, $description, $frequency, $categoryId);
    }

    public function delete(int $id): void
    {
        Habit::delete($id);
    }

    public function getAllWithStatusForDate(string $date): array
    {
        return Habit::getAllWithStatusForDate($date);
    }

    public function toggleForDate(int $habitId, string $date): void
    {
        Habit::toggleForDate($habitId, $date);
    }
}