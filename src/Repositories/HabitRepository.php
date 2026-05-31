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

    public function findById(int $id): ?Habit
    {
        return Habit::findById($id);
    }

    public function save(Habit $habit): void
    {
        $habit->save();
    }

    public function delete(Habit $habit): void
    {
        $habit->delete();
    }

    public function getAllWithStatusForDate(string $date): array
    {
        return Habit::getAllWithStatusForDate($date);
    }

    public function toggleForDate(int $habitId, string $date): void
    {
        $habit = $this->findById($habitId);

        if ($habit === null) {
            return;
        }

        $habit->toggleForDate($date);
    }
}
