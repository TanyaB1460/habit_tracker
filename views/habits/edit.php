<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование привычки</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
<div class="page">
    <header class="page-header">
        <div class="page-heading">
            <h1 class="page-title">Редактировать привычку</h1>
            <p class="page-subtitle">Изменение названия, описания, частоты и категории</p>
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
                <h2 class="card-title">Форма редактирования</h2>

                <form method="POST" action="/habits/update" class="form">
                    <input type="hidden" name="id" value="<?= (int) $habit['id'] ?>">

                    <div class="form-group">
                        <label class="form-label" for="name">Название</label>
                        <input
                                class="form-input"
                                type="text"
                                id="name"
                                name="name"
                                value="<?= htmlspecialchars((string) $habit['name'], ENT_QUOTES, 'UTF-8') ?>"
                                required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Описание</label>
                        <textarea
                                class="form-textarea"
                                id="description"
                                name="description"
                                rows="4"
                        ><?= htmlspecialchars((string) ($habit['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="frequency">Частота</label>
                        <select class="form-select" id="frequency" name="frequency">
                            <option value="daily" <?= ($habit['frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>
                                ежедневно
                            </option>
                            <option value="weekly" <?= ($habit['frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>
                                еженедельно
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Категория</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="">Без категории</option>
                            <?php foreach ($categories as $category): ?>
                                <option
                                        value="<?= (int) $category['id'] ?>"
                                        <?= ((int) ($habit['category_id'] ?? 0) === (int) $category['id']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="actions">
                        <button class="button button-primary" type="submit">Сохранить</button>
                        <a class="button button-secondary" href="/habits">Отмена</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>
</body>
</html>