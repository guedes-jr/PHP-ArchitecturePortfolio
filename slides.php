<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare("
    SELECT
        s.id,
        s.file_path,
        s.cover_image,
        s.downloads,
        st.title,
        st.slug,
        st.description
    FROM slides s
    INNER JOIN slide_translations st
        ON st.slide_id = s.id AND st.language = :language
    WHERE s.status = 'published'
    ORDER BY s.created_at DESC
");

$stmt->execute([
    'language' => $lang,
]);

$slides = $stmt->fetchAll();

?>

<main>
    <section class="page-hero">
        <span><?= escape($t['publications']); ?></span>
        <h1><?= escape($t['publications']); ?></h1>
        <p>Apresentações, portfólios, publicações e materiais institucionais disponíveis para consulta.</p>
    </section>

    <section class="projects-list-section">
        <?php if (!$slides): ?>
            <p>Nenhuma publicação disponível ainda.</p>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($slides as $slide): ?>
                    <article class="card">
                        <?php if ($slide['cover_image']): ?>
                            <div
                                class="card-image"
                                style="background-image: url('<?= BASE_URL . '/' . escape($slide['cover_image']); ?>');"
                            ></div>
                        <?php else: ?>
                            <div class="card-image"></div>
                        <?php endif; ?>

                        <h3><?= escape($slide['title']); ?></h3>

                        <p>
                            <?= escape((string) $slide['downloads']); ?> downloads
                        </p>

                        <div class="card-summary">
                            <?= escape($slide['description']); ?>
                        </div>

                        <a
                            href="<?= BASE_URL; ?>/download.php?id=<?= escape((string) $slide['id']); ?>"
                            class="card-link"
                            target="_blank"
                        >
                            Baixar material →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>