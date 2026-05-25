<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Database;
use App\Repositories\HabitRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class HabitRepositoryIntegrationTest extends TestCase
{
    private PDO $pdo;
    private HabitRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

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
        unset($this->pdo);

        parent::tearDown();
    }

    public function testCreateHabitPersistsRow(): void
    {
        $this->repository->create('Учёба', 'Читать 30 минут', 'daily', null);

        $stmt = $this->pdo->query(
            'SELECT id, name, description, frequency, is_active FROM habits'
        );
        $rows = $stmt->fetchAll();

        $this->assertCount(1, $rows);
        $this->assertSame('Учёба', $rows[0]['name']);
        $this->assertSame('Читать 30 минут', $rows[0]['description']);
        $this->assertSame('daily', $rows[0]['frequency']);
        $this->assertSame(1, (int) $rows[0]['is_active']);
    }

    public function testUpdateHabitUpdatesRow(): void
    {
        $this->pdo->exec(
            "INSERT INTO habits (name, description, frequency, is_active)
             VALUES ('Учёба', 'старое описание', 'daily', 1)"
        );
        $id = (int) $this->pdo->lastInsertId();

        $this->repository->update($id, 'Работа', 'новое описание', 'weekly', null);

        $stmt = $this->pdo->prepare(
            'SELECT name, description, frequency
             FROM habits
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        $this->assertSame('Работа', $row['name']);
        $this->assertSame('новое описание', $row['description']);
        $this->assertSame('weekly', $row['frequency']);
    }

    public function testDeleteHabitRemovesRow(): void
    {
        $this->pdo->exec(
            "INSERT INTO habits (name, description, frequency, is_active)
             VALUES ('Уборка', NULL, 'weekly', 1)"
        );
        $id = (int) $this->pdo->lastInsertId();

        $this->repository->delete($id);

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM habits WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $count = (int) $stmt->fetchColumn();

        $this->assertSame(0, $count);
    }

    private function createSchema(): void
    {
        $this->pdo->exec(
            'CREATE TABLE habits (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                frequency VARCHAR(20) NOT NULL,
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