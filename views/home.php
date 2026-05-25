<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Трекер привычек</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
<div class="page">
    <header class="page-header">
        <div class="page-heading">
            <h1 class="page-title">Трекер привычек</h1>
            <p class="page-date">
                Сегодня: <?= htmlspecialchars((string) $today, ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <nav class="nav" aria-label="Основная навигация">
            <a class="nav-link" href="/">Главная</a>
            <a class="nav-link" href="/habits">Привычки</a>
            <a class="nav-link" href="/stats">Статистика</a>
        </nav>
    </header>

    <main id="main" class="content">
        <?php if (empty($habits)): ?>
            <section class="empty-state">
                <p class="empty-text">Пока нет привычек.</p>
                <a class="button button-primary" href="/habits">Добавить привычку</a>
            </section>
        <?php else: ?>
            <section class="habits-list">
                <?php foreach ($habits as $habit): ?>
                    <?php
                    $frequencyLabel = match ($habit['frequency']) {
                        'daily' => 'ежедневно',
                        'weekly' => 'еженедельно',
                        default => (string) $habit['frequency'],
                    };

                    $isCompleted = (bool) ($habit['completed_today'] ?? false);
                    $statusClass = $isCompleted ? 'status-done' : 'status-pending';
                    $statusText = $isCompleted ? 'выполнено' : 'не выполнено';
                    $buttonText = $isCompleted ? 'Отменить отметку' : 'Отметить выполнение';
                    ?>
                    <article class="habit-card">
                        <div class="habit-card__top">
                            <h2 class="habit-title">
                                <?= htmlspecialchars((string) $habit['name'], ENT_QUOTES, 'UTF-8') ?>
                            </h2>

                            <span class="status-badge <?= $statusClass ?>">
                                <?= $statusText ?>
                            </span>
                        </div>

                        <?php if (!empty($habit['description'])): ?>
                            <p class="habit-description">
                                <?= htmlspecialchars((string) $habit['description'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>

                        <p class="habit-meta">
                            Частота:
                            <?= htmlspecialchars($frequencyLabel, ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <form method="POST" action="/toggle" class="habit-form">
                            <input type="hidden" name="habit_id" value="<?= (int) $habit['id'] ?>">
                            <input
                                    type="hidden"
                                    name="date"
                                    value="<?= htmlspecialchars((string) $today, ENT_QUOTES, 'UTF-8') ?>"
                            >

                            <button
                                    class="button <?= $isCompleted ? 'button-secondary' : 'button-primary' ?>"
                                    type="submit"
                            >
                                <?= $buttonText ?>
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>