<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибка сервера</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
<div class="page">
    <main class="content">
        <section class="error-page">
            <div class="error-box">
                <p class="error-code">500</p>
                <h1 class="page-title">Что-то пошло не так</h1>
                <p class="page-subtitle">
                    На сервере произошла ошибка. Попробуй обновить страницу чуть позже.
                </p>

                <div class="actions">
                    <a class="button button-primary" href="/">На главную</a>
                    <a class="button button-secondary" href="/stats">К статистике</a>
                </div>
            </div>
        </section>
    </main>
</div>
</body>
</html>