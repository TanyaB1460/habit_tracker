<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Трекер привычек</title>
</head>
<body>
<h1>Трекер привычек</h1>

<p>Сегодня: <?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?></p>

<p>
    <a href="/habits">Привычки</a> |
    <a href="/stats">Статистика</a>
</p>

<?php if (empty($habits)): ?>
    <p>Пока нет привычек.</p>
    <p><a href="/habits">Добавить привычку</a></p>
<?php else: ?>
    <?php foreach ($habits as $habit): ?>
        <?php
        $frequencyLabel = match ($habit['frequency']) {
            'daily' => 'ежедневно',
            'weekly' => 'еженедельно',
            default => $habit['frequency'],
        };
        ?>
        <hr>
        <h2><?= htmlspecialchars($habit['name'], ENT_QUOTES, 'UTF-8') ?></h2>

        <?php if (!empty($habit['description'])): ?>
            <p><?= htmlspecialchars($habit['description'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <p>
            Частота: <?= htmlspecialchars($frequencyLabel, ENT_QUOTES, 'UTF-8') ?><br>
            Статус: <?= $habit['completed_today'] ? 'выполнено' : 'не выполнено' ?>
        </p>

        <form method="POST" action="/habits/toggle">
            <input type="hidden" name="habit_id" value="<?= (int) $habit['id'] ?>">
            <input type="hidden" name="date" value="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit">
                <?= $habit['completed_today'] ? 'Отменить' : 'Отметить' ?>
            </button>
        </form>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>