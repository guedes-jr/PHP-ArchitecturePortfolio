<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT thumbnail FROM videos WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $video = $stmt->fetch();

    if ($video && $video['thumbnail']) {
        $imagePath = __DIR__ . '/../../' . $video['thumbnail'];

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $deleteStmt = $pdo->prepare('DELETE FROM videos WHERE id = :id');
    $deleteStmt->execute(['id' => $id]);
}

redirect(BASE_URL . '/admin/videos/index.php');