<?php
$services = [
    'frontend' => 'nginx:1.23-alpine',
    'backend' => 'php:7.4-fpm',
    'network' => 'internal',
];
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>PHP-FPM check</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
    <main>
        <h1>Проверка связки nginx и PHP-FPM</h1>
        <p>Ответ сформирован PHP-контейнером, а HTTP-запрос принят контейнером nginx.</p>
        <ul>
            <?php foreach ($services as $name => $value): ?>
                <li><strong><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>:</strong> <?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
