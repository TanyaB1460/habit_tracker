<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use Throwable;

final class Habit
{
    public function __construct(
        public readonly ?int $id,
        public string $name,
        public ?string $description,
        public string $frequency,
        public ?int $categoryId,
        public bool $isActive = true,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $categoryName = null,
    ) {
    }

    //Сохранить новую привычку или обновить существующую
    public function save(): void
    {
        $pdo = Database::getConnection();

        if ($this->id === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO habits (name, description, frequency, category_id)
                 VALUES (:name, :description, :frequency, :category_id)'
            );
            $stmt->execute([
                'name' => $this->name,
                'description' => $this->description,
                'frequency' => $this->frequency,
                'category_id' => $this->categoryId,
            ]);
        } else {
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
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'frequency' => $this->frequency,
                'category_id' => $this->categoryId,
            ]);
        }
    }

    //Удалить привычку из базы по идентификатору
    public function delete(): void
    {
        if ($this->id === null) {
            return;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM habits WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
    }

    //Создать объект модели из строки результата запроса
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            frequency: $row['frequency'],
            categoryId: isset($row['category_id']) ? (int) $row['category_id'] : null,
            isActive: (bool) ($row['is_active'] ?? true),
            createdAt: $row['created_at'] ?? null,
            updatedAt: $row['updated_at'] ?? null,
            categoryName: $row['category_name'] ?? null,
        );
    }

    #[\NoDiscard('Результат поиска привычки нужно проверить на null.')]
    public static function findById(int $id): ?self
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare(
                'SELECT h.id, h.name, h.description, h.frequency, h.category_id,
                        c.name AS category_name, h.is_active, h.created_at, h.updated_at
                 FROM habits h
                 LEFT JOIN habit_categories c ON c.id = h.category_id
                 WHERE h.id = :id
                 LIMIT 1'
            );
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();

            return $row ? self::fromRow($row) : null;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    #[\NoDiscard('Список привычек должен быть использован в вызывающем коде.')]
    public static function getAll(): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->query(
                'SELECT h.id, h.name, h.description, h.frequency, h.category_id,
                        c.name AS category_name, h.is_active, h.created_at, h.updated_at
                 FROM habits h
                 LEFT JOIN habit_categories c ON c.id = h.category_id
                 WHERE h.is_active = TRUE
                 ORDER BY h.id DESC'
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

    //Получить привычки со статусом выполнения на дату
    public static function getAllWithStatusForDate(string $date): array
    {
        try {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare(
                'SELECT h.id, h.name, h.description, h.frequency, h.category_id,
                        c.name AS category_name, h.is_active, h.created_at, h.updated_at,
                        EXISTS (
                            SELECT 1 FROM habit_logs hl
                            WHERE hl.habit_id = h.id AND hl.completed_on = :date
                        ) AS completed_today
                 FROM habits h
                 LEFT JOIN habit_categories c ON c.id = h.category_id
                 WHERE h.is_active = TRUE
                 ORDER BY h.id DESC'
            );
            $stmt->execute(['date' => $date]);

            return array_map(
                static fn(array $row) => [
                    'habit' => self::fromRow($row),
                    'completedToday' => (bool) $row['completed_today'],
                ],
                $stmt->fetchAll()
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    #[\NoDiscard('Сводка статистики нужна для отображения или дальнейшей обработки.')]
    public static function getStatsSummary(): array
    {
        try {
            $pdo = Database::getConnection();
            $totalStmt = $pdo->query('SELECT COUNT(*) FROM habits WHERE is_active = TRUE');

            return [
                'total_habits' => (int) $totalStmt->fetchColumn(),
                'total_logs' => HabitLog::countAll(),
                'completed_today' => HabitLog::countByDate(date('Y-m-d')),
            ];
        } catch (Throwable $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    //Получить статистику выполнения по дням
    public static function getDailyCompletionStats(int $days = 14): array
    {
        return HabitLog::getDailyStats($days);
    }

    //Переключить статус выполнения привычки на дату
    public function toggleForDate(string $date): void
    {
        $existing = HabitLog::findByHabitAndDate((int) $this->id, $date);

        if ($existing !== null) {
            $existing->delete();
            return;
        }

        $log = new HabitLog(id: null, habitId: (int) $this->id, completedOn: $date);
        $log->save();
    }
}
