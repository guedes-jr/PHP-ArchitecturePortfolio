<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("
        UPDATE contact_messages
        SET status = 'read'
        WHERE id = :id
    ");

    $stmt->execute(['id' => $id]);
}

redirect(BASE_URL . '/admin/messages/index.php');