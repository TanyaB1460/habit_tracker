<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    //Получить (или создать) общее PDO-подключение
    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $dbname = $_ENV['DB_NAME'] ?? 'habit_tracker';
        $user = $_ENV['DB_USER'] ?? 'postgres';
        $password = $_ENV['DB_PASS'] ?? '';

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $dbname);

        try {
            self::$instance = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new PDOException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        return self::$instance;
    }

    //Установить внешнее PDO-подключение
    public static function setConnection(PDO $pdo): void
    {
        self::$instance = $pdo;
    }

    //Сбросить текущее подключение
    public static function reset(): void
    {
        self::$instance = null;
    }

    //Запретить создание экземпляров класса
    private function __construct()
    {
    }
}
