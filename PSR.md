# Habit Tracker

Персональный трекер привычек на PHP 8.5 с PostgreSQL.

## Стек

- PHP 8.5
- PostgreSQL
- Composer (PSR-4 автозагрузка)
- Monolog (PSR-3 логгирование)
- nyholm/psr7 (PSR-7 запросы/ответы)
- psr/http-server-middleware (PSR-15 Middleware)
- PHPUnit 11

## Архитектура

```
public/index.php        — Frontend-контроллер: инициализирует окружение, PSR-7 запрос, логгер
src/Core/Router.php     — Router: читает #[Route] атрибуты через Reflection, диспатчит запросы
src/Core/Controller.php — Базовый абстрактный контроллер
src/Core/Database.php   — Singleton PDO-обёртка
src/Core/Attributes/Route.php        — PHP 8 Attribute для маршрутов
src/Core/Middleware/LoggerMiddleware.php — PSR-15 Middleware (включается при APP_DEBUG=true)
src/Controllers/        — HomeController, HabitController, StatsController
src/Models/             — ActiveRecord-модели: Habit, HabitCategory, HabitLog
src/Repositories/       — HabitRepository — query-слой поверх моделей
src/Config/env.php      — Кастомный загрузчик .env
```

### Паттерн моделей — ActiveRecord

Каждая модель (`Habit`, `HabitCategory`, `HabitLog`) — объект записи с публичными свойствами и методами `save()` / `delete()`. Репозиторий (`HabitRepository`) предоставляет query-методы (`getAll`, `findById`, `getAllWithStatusForDate`), сохраняя чистое разделение слоёв.

## Установка

```bash
cp .env.example .env   # заполнить DB_HOST, DB_NAME, DB_USER, DB_PASS
composer install
# создать БД и применить миграции
php -S localhost:8080 -t public
```

## Конфигурация (.env)

```
APP_NAME="Трекер привычек"
APP_ENV=dev
APP_DEBUG=true
DB_HOST=127.0.0.1
DB_PORT=5432
DB_NAME=habit_tracker
DB_USER=postgres
DB_PASS=your_password
```

## Тесты

```bash
./vendor/bin/phpunit --coverage-text
```

## PSR-12 (линтер)

Конфигурация: `phpcs.xml` — проверяет весь `src/` на соответствие PSR-12.

```bash
./vendor/bin/phpcs
```

Пример вывода после правок:

```
FILE: src/Core/Database.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Core/Router.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Core/Controller.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Controllers/HabitController.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Controllers/HomeController.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Controllers/StatsController.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Models/Habit.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Models/HabitCategory.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Models/HabitLog.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

FILE: src/Repositories/HabitRepository.php
--------------------------------------------------------------------------------
FOUND 0 ERRORS AND 0 WARNINGS AFFECTING 0 LINES
--------------------------------------------------------------------------------

Time: 543ms; Memory: 24MB
```

Чтобы автоматически исправить стиль:

```bash
./vendor/bin/phpcbf
```

## PSR-соответствие

| Стандарт | Реализация |
|----------|------------|
| PSR-3 (логгирование) | monolog/monolog, `LoggerInterface` |
| PSR-4 (автозагрузка) | Composer, `App\\ => src/` |
| PSR-7 (HTTP-сообщения) | nyholm/psr7, `ServerRequestInterface` / `ResponseInterface` |
| PSR-15 (Middleware) | `LoggerMiddleware implements MiddlewareInterface` |
| PSR-12 (стиль кода) | phpcs с ruleset PSR12 |
