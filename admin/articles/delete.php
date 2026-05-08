<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT cover_image FROM articles WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $article = $stmt->fetch();

    if ($article && $article['cover_image']) {
        $imagePath = __DIR__ . '/../../' . $article['cover_image'];

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $deleteStmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $deleteStmt->execute(['id' => $id]);
}

redirect(BASE_URL . '/admin/articles/index.php');