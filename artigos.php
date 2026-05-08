<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare("
    SELECT
        a.id,
        a.cover_image,
        a.author,
        a.created_at,
        at.title,
        at.slug,
        at.summary
    FROM articles a
    INNER JOIN article_translations at
        ON at.article_id = a.id AND at.language = :language
    WHERE a.status = 'published'
    ORDER BY a.created_at DESC
");

$stmt->execute([
    'language' => $lang,
]);

$articles = $stmt->fetchAll();

?>

<main>
    <section class="page-hero">
        <span><?= escape($t['articles']); ?></span>
        <h1><?= escape($t['articles']); ?></h1>
        <p>Reflexões, publicações e conteúdos sobre arquitetura, design e cultura contemporânea.</p>
    </section>

    <section class="projects-list-section">
        <?php if (!$articles): ?>
            <p>Nenhum artigo publicado ainda.</p>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($articles as $article): ?>
                    <article class="card">
                        <?php if ($article['cover_image']): ?>
                            <div
                                class="card-image"
                                style="background-image: url('<?= BASE_URL . '/' . escape($article['cover_image']); ?>');"
                            ></div>
                        <?php else: ?>
                            <div class="card-image"></div>
                        <?php endif; ?>

                        <h3><?= escape($article['title']); ?></h3>
                        <p><?= escape(date('d/m/Y', strtotime($article['created_at']))); ?></p>

                        <div class="card-summary">
                            <?= escape($article['summary']); ?>
                        </div>

                        <a
                            href="<?= BASE_URL; ?>/artigo.php?slug=<?= escape($article['slug']); ?>&lang=<?= escape($lang); ?>"
                            class="card-link"
                        >
                            Ler artigo →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>