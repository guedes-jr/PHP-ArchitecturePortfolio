<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare("
    SELECT
        v.id,
        v.video_url,
        v.thumbnail,
        vt.title,
        vt.slug,
        vt.description
    FROM videos v
    INNER JOIN video_translations vt
        ON vt.video_id = v.id AND vt.language = :language
    WHERE v.status = 'published'
    ORDER BY v.created_at DESC
");

$stmt->execute([
    'language' => $lang,
]);

$videos = $stmt->fetchAll();

?>

<main>
    <section class="page-hero">
        <span><?= escape($t['videos']); ?></span>
        <h1><?= escape($t['videos']); ?></h1>
        <p>Conteúdos audiovisuais, entrevistas, apresentações e registros de projetos.</p>
    </section>

    <section class="projects-list-section">
        <?php if (!$videos): ?>
            <p>Nenhum vídeo publicado ainda.</p>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($videos as $video): ?>
                    <article class="card">
                        <?php if ($video['thumbnail']): ?>
                            <div
                                class="card-image"
                                style="background-image: url('<?= BASE_URL . '/' . escape($video['thumbnail']); ?>');"
                            ></div>
                        <?php else: ?>
                            <div class="card-image"></div>
                        <?php endif; ?>

                        <h3><?= escape($video['title']); ?></h3>

                        <div class="card-summary">
                            <?= escape($video['description']); ?>
                        </div>

                        <a
                            href="<?= escape($video['video_url']); ?>"
                            class="card-link"
                            target="_blank"
                        >
                            Assistir vídeo →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>