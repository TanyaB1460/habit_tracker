<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Database;
use App\Models\Habit;
use App\Repositories\HabitRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class HabitRepositoryIntegrationTest extends TestCase
{
    private PDO $pdo;
    private HabitRepository $repository;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->createSchema();
        Database::setConnection($this->pdo);

        $this->repository = new HabitRepository();
    }

    protected function tearDown(): void
    {
        Database::reset();
        parent::tearDown();
    }

    public function testSaveNewHabitPersistsRow(): void
    {
        $habit = new Habit(
            id: null,
            name: 'Читать',
            description: '30 минут',
            frequency: 'daily',
            categoryId: null,
        );

        $this->repository->save($habit);

        $stmt = $this->pdo->query('SELECT name, description, frequency, is_active FROM habits');
        $rows = $stmt->fetchAll();

        $this->assertCount(1, $rows);
        $this->assertSame('Читать', $rows[0]['name']);
        $this->assertSame('30 минут', $rows[0]['description']);
        $this->assertSame('daily', $rows[0]['frequency']);
        $this->assertSame(1, (int) $rows[0]['is_active']);
    }

    public function testSaveExistingHabitUpdatesRow(): void
    {
        $this->pdo->exec(
            "INSERT INTO habits (name, description, frequency, is_active)
             VALUES ('Старое', 'Старое описание', 'daily', 1)"
        );
        $id = (int) $this->pdo->lastInsertId();

        $habit = $this->repository->findById($id);
        $this->assertNotNull($habit);

        $habit->name = 'Новое';
        $habit->description = 'Новое описание';
        $habit->frequency = 'weekly';
        $this->repository->save($habit);

        $stmt = $this->pdo->prepare('SELECT name, description, frequency FROM habits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        $this->assertSame('Новое', $row['name']);
        $this->assertSame('Новое описание', $row['description']);
        $this->assertSame('weekly', $row['frequency']);
    }

    public function testDeleteHabitRemovesRow(): void
    {
        $this->pdo->exec(
            "INSERT INTO habits (name, description, frequency, is_active)
             VALUES ('Уборка', NULL, 'weekly', 1)"
        );
        $id = (int) $this->pdo->lastInsertId();

        $habit = $this->repository->findById($id);
        $this->assertNotNull($habit);

        $this->repository->delete($habit);

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM habits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $this->assertSame(0, (int) $stmt->fetchColumn());
    }

    public function testFindByIdReturnsNullForMissingHabit(): void
    {
        $result = $this->repository->findById(999);
        $this->assertNull($result);
    }

    public function testGetAllReturnsOnlyActiveHabits(): void
    {
        $this->pdo->exec(
            "INSERT INTO habits (name, description, frequency, is_active) VALUES
             ('Активная', NULL, 'daily', 1),
             ('Неактивная', NULL, 'daily', 0)"
        );

        $habits = $this->repository->getAll();

        $this->assertCount(1, $habits);
        $this->assertSame('Активная', $habits[0]->name);
    }

    public function testToggleForDateCreatesAndDeletesLog(): void
    {
        $this->pdo->exec(
            "INSERT INTO habits (name, description, frequency, is_active)
             VALUES ('Спорт', NULL, 'daily', 1)"
        );
        $habitId = (int) $this->pdo->lastInsertId();

        $this->repository->toggleForDate($habitId, '2024-05-01');
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM habit_logs');
        $this->assertSame(1, (int) $stmt->fetchColumn());

        $this->repository->toggleForDate($habitId, '2024-05-01');
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM habit_logs');
        $this->assertSame(0, (int) $stmt->fetchColumn());
    }

    private function createSchema(): void
    {
        $this->pdo->exec(
            'CREATE TABLE habits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                frequency VARCHAR(20) NOT NULL DEFAULT "daily",
                category_id INTEGER NULL,
                is_active BOOLEAN NOT NULL DEFAULT 1,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $this->pdo->exec(
            'CREATE TABLE habit_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $this->pdo->exec(
            'CREATE TABLE habit_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                habit_id INTEGER NOT NULL,
                completed_on DATE NOT NULL,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )'
        );
    }
}
