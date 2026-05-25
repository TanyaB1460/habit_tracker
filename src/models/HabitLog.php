<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

final class HabitLog
{
    public static function findByHabitAndDate(int $habitId, string $date): ?array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT id, habit_id, completed_on, created_at
                 FROM habit_logs
                 WHERE habit_id = :habit_id
                   AND completed_on = :completed_on
                 LIMIT 1'
            );

            $stmt->execute([
                'habit_id' => $habitId,
                'completed_on' => $date,
            ]);

            $log = $stmt->fetch();

            return $log ?: null;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function create(int $habitId, string $date): void
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'INSERT INTO habit_logs (habit_id, completed_on)
                 VALUES (:habit_id, :completed_on)'
            );

            $stmt->execute([
                'habit_id' => $habitId,
                'completed_on' => $date,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function deleteByHabitAndDate(int $habitId, string $date): void
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'DELETE FROM habit_logs
                 WHERE habit_id = :habit_id
                   AND completed_on = :completed_on'
            );

            $stmt->execute([
                'habit_id' => $habitId,
                'completed_on' => $date,
            ]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function countAll(): int
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->query('SELECT COUNT(*) FROM habit_logs');

            return (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function countByDate(string $date): int
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT COUNT(*)
                 FROM habit_logs
                 WHERE completed_on = :completed_on'
            );

            $stmt->execute([
                'completed_on' => $date,
            ]);

            return (int) $stmt->fetchColumn();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function getDailyStats(int $days = 14): array
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