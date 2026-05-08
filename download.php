<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect(BASE_URL . '/slides.php');
}

$stmt = $pdo->prepare("
    SELECT file_path
    FROM slides
    WHERE id = :id
      AND status = 'published'
    LIMIT 1
");

$stmt->execute(['id' => $id]);
$slide = $stmt->fetch();

if (!$slide) {
    redirect(BASE_URL . '/slides.php');
}

$updateStmt = $pdo->prepare("
    UPDATE slides
    SET downloads = downloads + 1
    WHERE id = :id
");

$updateStmt->execute(['id' => $id]);

redirect(BASE_URL . '/' . $slide['file_path']);