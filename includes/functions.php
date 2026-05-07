<?php

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

function escape(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['admin_user']);
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function uploadFile(array $file, string $targetDir): ?string
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $originalName = $file['name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return null;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $filename = uniqid('upload_', true) . '.' . $extension;
    $destination = rtrim($targetDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return str_replace(__DIR__ . '/../', '', $destination);
}