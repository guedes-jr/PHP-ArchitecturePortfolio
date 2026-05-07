<?php

require_once __DIR__ . '/includes/db.php';

if ($pdo instanceof PDO) {
    echo 'Conexão com o banco funcionando.';
}