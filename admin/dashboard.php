<?php

$pageTitle = 'Dashboard Admin';

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/header.php';

$totalVisits = $pdo->query('SELECT COUNT(*) AS total FROM site_visits')->fetch()['total'] ?? 0;

$todayVisitsStmt = $pdo->query("
    SELECT COUNT(*) AS total
    FROM site_visits
    WHERE DATE(created_at) = CURDATE()
");
$todayVisits = $todayVisitsStmt->fetch()['total'] ?? 0;

$articlesCount = $pdo->query('SELECT COUNT(*) AS total FROM articles')->fetch()['total'] ?? 0;
$projectsCount = $pdo->query('SELECT COUNT(*) AS total FROM projects')->fetch()['total'] ?? 0;
$videosCount = $pdo->query('SELECT COUNT(*) AS total FROM videos')->fetch()['total'] ?? 0;
$slidesCount = $pdo->query('SELECT COUNT(*) AS total FROM slides')->fetch()['total'] ?? 0;
$messagesCount = $pdo->query('SELECT COUNT(*) AS total FROM contact_messages')->fetch()['total'] ?? 0;

$unreadMessagesCount = $pdo->query("
    SELECT COUNT(*) AS total
    FROM contact_messages
    WHERE status = 'unread'
")->fetch()['total'] ?? 0;

$topPagesStmt = $pdo->query("
    SELECT page_url, COUNT(*) AS total
    FROM site_visits
    GROUP BY page_url
    ORDER BY total DESC
    LIMIT 5
");
$topPages = $topPagesStmt->fetchAll();

?>

<?php require_once __DIR__ . '/includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Dashboard</h1>
    <p>Bem-vindo, <?= escape($_SESSION['admin_user']['name']); ?>.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <span>Acessos totais</span>
            <strong><?= escape((string) $totalVisits); ?></strong>
        </div>

        <div class="stat-card">
            <span>Acessos hoje</span>
            <strong><?= escape((string) $todayVisits); ?></strong>
        </div>

        <div class="stat-card">
            <span>Projetos</span>
            <strong><?= escape((string) $projectsCount); ?></strong>
        </div>

        <div class="stat-card">
            <span>Artigos</span>
            <strong><?= escape((string) $articlesCount); ?></strong>
        </div>

        <div class="stat-card">
            <span>Vídeos</span>
            <strong><?= escape((string) $videosCount); ?></strong>
        </div>

        <div class="stat-card">
            <span>Slides</span>
            <strong><?= escape((string) $slidesCount); ?></strong>
        </div>
        <div class="stat-card">
            <span>Mensagens</span>
            <strong><?= escape((string) $messagesCount); ?></strong>
        </div>

        <div class="stat-card">
            <span>Não lidas</span>
            <strong><?= escape((string) $unreadMessagesCount); ?></strong>
        </div>
    </div>

    <div class="admin-panel">
        <h2>Páginas mais acessadas</h2>

        <?php if (!$topPages): ?>
            <p>Nenhum acesso registrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Página</th>
                        <th>Acessos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topPages as $page): ?>
                        <tr>
                            <td><?= escape($page['page_url']); ?></td>
                            <td><?= escape((string) $page['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>