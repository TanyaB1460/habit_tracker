<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование привычки</title>
</head>
<body>
<p>
    <a href="/">Главная</a> |
    <a href="/habits">Привычки</a> |
    <a href="/stats">Статистика</a>
</p>

<h1>Редактировать привычку</h1>

<form method="POST" action="/habits/update">
    <input type="hidden" name="id" value="<?= (int) $habit['id'] ?>">

    <p>
        <label for="name">Название</label><br>
        <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($habit['name'], ENT_QUOTES, 'UTF-8') ?>"
                required
        >
    </p>

    <p>
        <label for="description">Описание</label><br>
        <textarea id="description" name="description" rows="4" cols="40"><?= htmlspecialchars($habit['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </p>

    <p>
        <label for="frequency">Частота</label><br>
        <select id="frequency" name="frequency">
            <option value="daily" <?= ($habit['frequency'] ?? '') === 'daily' ? 'selected' : '' ?>>ежедневно</option>
            <option value="weekly" <?= ($habit['frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>еженедельно</option>
        </select>
    </p>

    <p>
        <button type="submit">Сохранить</button>
    </p>
</form>
</body>
</html>