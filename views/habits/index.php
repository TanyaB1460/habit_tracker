<?php

declare(strict_types=1);

/** @var \App\Models\Habit[] $habits */
/** @var \App\Models\HabitCategory[] $categories */

$frequencyLabels = [
        'daily' => 'ежедневно',
        'weekly' => 'еженедельно',
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Привычки</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
<div class="page">
    <header class="page-header">
        <div class="page-heading">
            <h1 class="page-title">Управление привычками</h1>
            <p class="page-subtitle">Создание, просмотр, редактирование и удаление привычек</p>
        </div>

        <nav class="nav" aria-label="Основная навигация">
            <a class="nav-link" href="/">Главная</a>
            <a class="nav-link" href="/habits">Привычки</a>
            <a class="nav-link" href="/stats">Статистика</a>
        </nav>
    </header>

    <main id="main" class="content">
        <section class="section">
            <div class="card">
                <h2 class="card-title">Добавить привычку</h2>

                <form method="POST" action="/habits/create" class="form">
                    <div class="form-group">
                        <label class="form-label" for="name">Название</label>
                        <input class="form-input" type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Описание</label>
                        <textarea class="form-textarea" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="frequency">Частота</label>
                        <select class="form-select" id="frequency" name="frequency">
                            <option value="daily">ежедневно</option>
                            <option value="weekly">еженедельно</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Категория</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Без категории</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category->id ?>">
                                    <?= htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="actions">
                        <button class="button button-primary" type="submit">Создать</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="section">
            <h2 class="card-title">Список привычек</h2>

            <?php if (empty($habits)): ?>
                <div class="empty-state">
                    <p class="empty-text">Привычек пока нет.</p>
                </div>
            <?php else: ?>
                <div class="habits-list">
                    <?php foreach ($habits as $habit):
                        $frequencyLabel = $frequencyLabels[$habit->frequency] ?? $habit->frequency;
                        ?>
                        <article class="habit-card">
                            <div class="habit-card__top">
                                <h3 class="habit-title">
                                    <?= htmlspecialchars($habit->name, ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                            </div>

                            <?php if (!empty($habit->description)): ?>
                                <p class="habit-description">
                                    <?= htmlspecialchars($habit->description, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>

                            <p class="habit-meta">
                                Частота: <?= htmlspecialchars($frequencyLabel, ENT_QUOTES, 'UTF-8') ?>
                            </p>

                            <?php if (!empty($habit->categoryName)): ?>
                                <p class="habit-meta">
                                    Категория:
                                    <?= htmlspecialchars($habit->categoryName, ENT_QUOTES, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>

                            <div class="actions">
                                <a class="button button-secondary"
                                   href="/habits/edit?id=<?= (int) $habit->id ?>">
                                    Редактировать
                                </a>

                                <form method="POST" action="/habits/delete" class="inline-form"
                                      data-confirm="Удалить привычку?">
                                    <input type="hidden" name="id" value="<?= (int) $habit->id ?>">
                                    <button class="button button-danger" type="submit">Удалить</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
</body>
</html>
