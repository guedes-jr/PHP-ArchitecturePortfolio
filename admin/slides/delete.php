<?php

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../auth.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT file_path, cover_image FROM slides WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $slide = $stmt->fetch();

    if ($slide) {
        if ($slide['file_path']) {
            $filePath = __DIR__ . '/../../' . $slide['file_path'];

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($slide['cover_image']) {
            $imagePath = __DIR__ . '/../../' . $slide['cover_image'];

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }

    $deleteStmt = $pdo->prepare('DELETE FROM slides WHERE id = :id');
    $deleteStmt->execute(['id' => $id]);
}

redirect(BASE_URL . '/admin/slides/index.php');