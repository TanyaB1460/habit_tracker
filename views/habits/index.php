<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Привычки</title>
</head>
<body>
<p>
    <a href="/">Главная</a> |
    <a href="/habits">Привычки</a> |
    <a href="/stats">Статистика</a>
</p>

<h1>Управление привычками</h1>

<h2>Добавить привычку</h2>

<form method="POST" action="/habits/create">
    <p>
        <label for="name">Название</label><br>
        <input type="text" id="name" name="name" required>
    </p>

    <p>
        <label for="description">Описание</label><br>
        <textarea id="description" name="description" rows="4" cols="40"></textarea>
    </p>

    <p>
        <label for="frequency">Частота</label><br>
        <select id="frequency" name="frequency">
            <option value="daily">ежедневно</option>
            <option value="weekly">еженедельно</option>
        </select>
    </p>

    <p>
        <button type="submit">Создать</button>
    </p>
</form>

<hr>

<h2>Список привычек</h2>

<?php
$frequencyLabels = [
        'daily' => 'ежедневно',
        'weekly' => 'еженедельно',
];
?>

<?php if (empty($habits)): ?>
    <p>Привычек пока нет.</p>
<?php else: ?>
    <?php foreach ($habits as $habit): ?>
        <article>
            <h3><?= htmlspecialchars($habit['name'], ENT_QUOTES, 'UTF-8') ?></h3>

            <?php if (!empty($habit['description'])): ?>
                <p><?= htmlspecialchars($habit['description'], ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <p>
                Частота:
                <?= htmlspecialchars($frequencyLabels[$habit['frequency']] ?? $habit['frequency'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>
                <a href="/habits/edit?id=<?= (int) $habit['id'] ?>">Редактировать</a>
            </p>

            <form method="POST" action="/habits/delete" onsubmit="return confirm('Удалить привычку?');">
                <input type="hidden" name="id" value="<?= (int) $habit['id'] ?>">
                <button type="submit">Удалить</button>
            </form>

            <hr>
        </article>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>