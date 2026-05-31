<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

final class HabitCategory
{
    public function __construct(
        public readonly ?int $id,
        public string $name,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {
    }

    public function save(): void
    {
        $pdo = Database::getConnection();

        if ($this->id === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO habit_categories (name) VALUES (:name)'
            );
            $stmt->execute(['name' => $this->name]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE habit_categories SET name = :name, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
            );
            $stmt->execute(['name' => $this->name, 'id' => $this->id]);
        }
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
        );
    }

    public static function findById(int $id): ?self
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare(
                'SELECT id, name, created_at, updated_at FROM habit_categories WHERE id = :id LIMIT 1'
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            return $row ? self::fromRow($row) : null;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }


    public static function getAll(): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query(
                'SELECT id, name, created_at, updated_at FROM habit_categories ORDER BY name ASC'
            );

            return array_map(
                static fn(array $row) => self::fromRow($row),
                $stmt->fetchAll()
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
