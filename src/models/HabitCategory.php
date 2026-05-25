<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

final class HabitCategory
{
    public static function getAll(): array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->query(
                'SELECT id, name, created_at, updated_at
                 FROM habit_categories
                 ORDER BY name ASC'
            );

            return $stmt->fetchAll();
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function findById(int $id): ?array
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'SELECT id, name, created_at, updated_at
                 FROM habit_categories
                 WHERE id = :id
                 LIMIT 1'
            );

            $stmt->execute(['id' => $id]);

            $category = $stmt->fetch();

            return $category ?: null;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public static function create(string $name): void
    {
        try {
            $pdo = Database::getConnection();

            $stmt = $pdo->prepare(
                'INSERT INTO habit_categories (name)
                 VALUES (:name)'
            );

            $stmt->execute(['name' => $name]);
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}