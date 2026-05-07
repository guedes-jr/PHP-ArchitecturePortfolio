<?php

require_once __DIR__ . '/../auth.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= escape($pageTitle ?? 'Painel Administrativo'); ?></title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/admin.css">
</head>
<body>

<header class="admin-header">
    <div>
        <strong>Painel Administrativo</strong>
    </div>

    <nav>
        <a href="<?= BASE_URL; ?>/index.php" target="_blank">Ver site</a>
        <a href="<?= BASE_URL; ?>/admin/logout.php">Sair</a>
    </nav>
</header>

<main class="admin-layout">