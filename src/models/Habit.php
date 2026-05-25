<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

final class Habit
{
    #[\NoDiscard('Список привычек должен быть использован')]
    public static function getAll(): array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->query(
                'SELECT h.id,
                        h.name,
                        h.description,
                        h.frequency,
                        h.category_id,
                        c.name AS category_name,
                        h.is_active,
                        h.created_at,
                        h.updated_at
                 FROM habits h
                 LEFT JOIN habit_categories c ON c.id = h.category_id
                 WHERE h.is_active = TRUE
                 ORDER BY h.id DESC'
            );

            return $stmt->fetchAll();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    #[\NoDiscard('Не забудьте использовать результат!')]
    public static function findById(int $id): ?array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT h.id,
                        h.name,
                        h.description,
                        h.frequency,
                        h.category_id,
                        c.name AS category_name,
                        h.is_active,
                        h.created_at,
                        h.updated_at
                 FROM habits h
                 LEFT JOIN habit_categories c ON c.id = h.category_id
                 WHERE h.id = :id
                 LIMIT 1'
            );

            $stmt->execute(['id' => $id]);

            $habit = $stmt->fetch();

            return $habit ?: null;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function create(
        string $name,
        ?string $description,
        string $frequency = 'daily',
        ?int $categoryId = null
    ): void {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'INSERT INTO habits (name, description, frequency, category_id)
                 VALUES (:name, :description, :frequency, :category_id)'
            );

            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'frequency' => $frequency,
                'category_id' => $categoryId,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function update(
        int $id,
        string $name,
        ?string $description,
        string $frequency,
        ?int $categoryId = null
    ): void {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'UPDATE habits
                 SET name = :name,
                     description = :description,
                     frequency = :frequency,
                     category_id = :category_id,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );

            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'frequency' => $frequency,
                'category_id' => $categoryId,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function delete(int $id): void
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare('DELETE FROM habits WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function toggleForDate(int $habitId, string $date): void
    {
        $existing = HabitLog::findByHabitAndDate($habitId, $date);

        if ($existing !== null) {
            HabitLog::deleteByHabitAndDate($habitId, $date);
            return;
        }

        HabitLog::create($habitId, $date);
    }

    #[\NoDiscard('Данные для главной страницы должны быть использованы')]
    public static function getAllWithStatusForDate(string $date): array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT h.id,
                        h.name,
                        h.description,
                        h.frequency,
                        h.category_id,
                        c.name AS category_name,
                        h.is_active,
                        h.created_at,
                        h.updated_at,
                        EXISTS (
                            SELECT 1
                            FROM habit_logs hl
                            WHERE hl.habit_id = h.id
                              AND hl.completed_on = :date
                        ) AS completed_today
                 FROM habits h
                 LEFT JOIN habit_categories c ON c.id = h.category_id
                 WHERE h.is_active = TRUE
                 ORDER BY h.id DESC'
            );

            $stmt->execute(['date' => $date]);

            return $stmt->fetchAll();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    #[\NoDiscard('Сводная статистика должна быть использована')]
    public static function getStatsSummary(): array
    {
        try {
            $pdo = Database::getConnection();

            $totalHabitsStmt = $pdo->query(
                'SELECT COUNT(*)
                 FROM habits
                 WHERE is_active = TRUE'
            );
            $totalHabits = (int) $totalHabitsStmt->fetchColumn();

            return [
                'total_habits' => $totalHabits,
                'total_logs' => HabitLog::countAll(),
                'completed_today' => HabitLog::countByDate(date('Y-m-d')),
            ];
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    #[\NoDiscard('Статистика по дням должна быть использована')]
    public static function getDailyCompletionStats(int $days = 14): array
    {
        return HabitLog::getDailyStats($days);
    }

}