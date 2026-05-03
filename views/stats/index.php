<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика</title>
</head>
<body>
<h1>Статистика</h1>

<p>
    <a href="/">Главная</a> |
    <a href="/habits">Привычки</a>
</p>

<h2>Общая информация</h2>

<u

<ul>
    >Всего активных привычек: <?= (int) ($stats['total_habits'] ?? 0) ?></l/li>
    >Всего отметок выполнения: <?= (int) ($stats['total_logs'] ?? 0) ?></l/li>
    >Выполнено сегодня: <?= (int) ($stats['completed_today'] ?? 0) ?></li>
</ul>

<h2>Активность за последние дни</h2>

<?php if (empty($dailyStats)): ?>
    <p>Пока нет данных для статистики.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Количество выполнений</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dailyStats as $row): ?>
            <tr>
                <td><?= htmlspecialchars((string) $row['completed_on'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= (int) $row['total'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>