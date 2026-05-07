<?php

require_once __DIR__ . '/db.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    return;
}

$pageUrl = $_SERVER['REQUEST_URI'] ?? '/';

if ($pageUrl === '/favicon.ico') {
    return;
}

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
$referer = $_SERVER['HTTP_REFERER'] ?? null;
$currentLanguage = $lang ?? 'pt';

$stmt = $pdo->prepare("
    INSERT INTO site_visits (
        page_url,
        language,
        ip_address,
        user_agent,
        referer
    ) VALUES (
        :page_url,
        :language,
        :ip_address,
        :user_agent,
        :referer
    )
");

$stmt->execute([
    'page_url' => $pageUrl,
    'language' => $currentLanguage,
    'ip_address' => $ipAddress,
    'user_agent' => $userAgent,
    'referer' => $referer,
]);