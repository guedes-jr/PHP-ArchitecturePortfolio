<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT cover_image FROM projects WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $project = $stmt->fetch();

    if ($project && $project['cover_image']) {
        $imagePath = __DIR__ . '/../../' . $project['cover_image'];

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $deleteStmt = $pdo->prepare('DELETE FROM projects WHERE id = :id');
    $deleteStmt->execute(['id' => $id]);
}

redirect(BASE_URL . '/admin/projects/index.php');