<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    redirect(BASE_URL . '/artigos.php?lang=' . $lang);
}

$stmt = $pdo->prepare("
    SELECT
        a.id,
        a.cover_image,
        a.author,
        a.views,
        a.created_at,
        at.title,
        at.summary,
        at.content
    FROM articles a
    INNER JOIN article_translations at
        ON at.article_id = a.id AND at.language = :language
    WHERE a.status = 'published'
      AND at.slug = :slug
    LIMIT 1
");

$stmt->execute([
    'language' => $lang,
    'slug' => $slug,
]);

$article = $stmt->fetch();

if (!$article) {
    redirect(BASE_URL . '/artigos.php?lang=' . $lang);
}

$updateViewsStmt = $pdo->prepare("
    UPDATE articles
    SET views = views + 1
    WHERE id = :id
");
$updateViewsStmt->execute([
    'id' => $article['id'],
]);

?>

<main>
    <section
        class="article-detail-hero"
        <?php if ($article['cover_image']): ?>
            style="background-image: url('<?= BASE_URL . '/' . escape($article['cover_image']); ?>');"
        <?php endif; ?>
    >
        <div>
            <span><?= escape(date('d/m/Y', strtotime($article['created_at']))); ?></span>
            <h1><?= escape($article['title']); ?></h1>
            <p><?= escape($article['summary']); ?></p>
        </div>
    </section>

    <section class="article-detail-content">
        <div class="article-meta">
            <span>Autor: <?= escape($article['author'] ?? ''); ?></span>
        </div>

        <div class="article-text">
            <?= nl2br(escape($article['content'])); ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>