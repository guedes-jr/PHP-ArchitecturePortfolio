<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/track_visit.php';

?>

<!DOCTYPE html>
<html lang="<?= escape($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape(SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="logo">
        <a href="<?= BASE_URL; ?>/index.php?lang=<?= escape($lang); ?>">
            <?= escape(SITE_NAME); ?>
        </a>
    </div>

    <nav class="main-menu">
        <a href="<?= BASE_URL; ?>/index.php?lang=<?= escape($lang); ?>"><?= escape($t['home']); ?></a>
        <a href="<?= BASE_URL; ?>/projetos.php?lang=<?= escape($lang); ?>"><?= escape($t['projects']); ?></a>
        <a href="<?= BASE_URL; ?>/artigos.php?lang=<?= escape($lang); ?>"><?= escape($t['articles']); ?></a>
        <a href="<?= BASE_URL; ?>/videos.php?lang=<?= escape($lang); ?>"><?= escape($t['videos']); ?></a>
        <a href="<?= BASE_URL; ?>/slides.php?lang=<?= escape($lang); ?>"><?= escape($t['publications']); ?></a>
        <a href="<?= BASE_URL; ?>/contato.php?lang=<?= escape($lang); ?>"><?= escape($t['contact']); ?></a>
    </nav>

    <div class="language-switcher">
        <a href="?lang=pt">PT</a>
        <a href="?lang=en">EN</a>
        <a href="?lang=es">ES</a>
    </div>
</header>