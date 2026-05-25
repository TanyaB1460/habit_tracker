<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
<div class="page">
    <header class="page-header">
        <div class="page-heading">
            <h1 class="page-title">Статистика</h1>
            <p class="page-subtitle">Сводка по привычкам и активности</p>
        </div>

        <nav class="nav" aria-label="Основная навигация">
            <a class="nav-link" href="/">Главная</a>
            <a class="nav-link" href="/habits">Привычки</a>
            <a class="nav-link" href="/stats">Статистика</a>
        </nav>
    </header>

    <main id="main" class="content">
        <section class="section">
            <h2 class="card-title">Общая информация</h2>

            <div class="stats-grid">
                <article class="stat-card">
                    <p class="stat-label">Всего активных привычек</p>
                    <p class="stat-value"><?= (int) ($summary['total_habits'] ?? 0) ?></p>
                </article>

                <article class="stat-card">
                    <p class="stat-label">Всего отметок выполнения</p>
                    <p class="stat-value"><?= (int) ($summary['total_logs'] ?? 0) ?></p>
                </article>

                <article class="stat-card">
                    <p class="stat-label">Выполнено сегодня</p>
                    <p class="stat-value"><?= (int) ($summary['completed_today'] ?? 0) ?></p>
                </article>
            </div>
        </section>

        <section class="section">
            <h2 class="card-title">Активность за последние дни</h2>

            <?php if (empty($dailyStats)): ?>
                <div class="empty-state">
                    <p class="empty-text">Пока нет данных для статистики.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Дата</th>
                            <th scope="col">Количество выполнений</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dailyStats as $row): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars((string) ($row['completed_on'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td><?= (int) ($row['total'] ?? 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>