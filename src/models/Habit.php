<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Throwable;

final class Habit
{
    #[\NoDiscard('Список привычек должен быть использован')]
    public static function getAll(): array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->query(
                'SELECT id, name, description, frequency, is_active, created_at, updated_at
                 FROM habits
                 WHERE is_active = TRUE
                 ORDER BY id DESC'
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
                'SELECT id, name, description, frequency, is_active, created_at, updated_at
                 FROM habits
                 WHERE id = :id
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

    public static function create(string $name, ?string $description, string $frequency = 'daily'): void
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'INSERT INTO habits (name, description, frequency)
                 VALUES (:name, :description, :frequency)'
            );

            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'frequency' => $frequency,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function update(int $id, string $name, ?string $description, string $frequency): void
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'UPDATE habits
                 SET name = :name,
                     description = :description,
                     frequency = :frequency,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );

            $stmt->execute([
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'frequency' => $frequency,
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
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT id
                 FROM habit_logs
                 WHERE habit_id = :habit_id AND completed_on = :completed_on
                 LIMIT 1'
            );
            $stmt->execute([
                'habit_id' => $habitId,
                'completed_on' => $date,
            ]);

            $existing = $stmt->fetch();

            if ($existing) {
                $deleteStmt = $pdo->prepare(
                    'DELETE FROM habit_logs
                     WHERE habit_id = :habit_id AND completed_on = :completed_on'
                );
                $deleteStmt->execute([
                    'habit_id' => $habitId,
                    'completed_on' => $date,
                ]);

                return;
            }

            $insertStmt = $pdo->prepare(
                'INSERT INTO habit_logs (habit_id, completed_on)
                 VALUES (:habit_id, :completed_on)'
            );
            $insertStmt->execute([
                'habit_id' => $habitId,
                'completed_on' => $date,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
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

            $totalHabitsStmt = $pdo->query('SELECT COUNT(*) FROM habits WHERE is_active = TRUE');
            $totalHabits = (int) $totalHabitsStmt->fetchColumn();

            $totalLogsStmt = $pdo->query('SELECT COUNT(*) FROM habit_logs');
            $totalLogs = (int) $totalLogsStmt->fetchColumn();

            $todayStmt = $pdo->prepare(
                'SELECT COUNT(*)
                 FROM habit_logs
                 WHERE completed_on = :today'
            );
            $todayStmt->execute(['today' => date('Y-m-d')]);
            $completedToday = (int) $todayStmt->fetchColumn();

            return [
                'total_habits' => $totalHabits,
                'total_logs' => $totalLogs,
                'completed_today' => $completedToday,
            ];
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }


    #[\NoDiscard('Статистика по дням должна быть использована')]
    public static function getDailyCompletionStats(int $days = 14): array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT completed_on, COUNT(*) AS total
                 FROM habit_logs
                 WHERE completed_on >= CURRENT_DATE - (:days * INTERVAL \'1 day\')
                 GROUP BY completed_on
                 ORDER BY completed_on DESC'
            );

            $stmt->bindValue(':days', $days, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}