<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.cover_image,
        pt.title,
        pt.slug,
        pt.location,
        pt.description
    FROM projects p
    INNER JOIN project_translations pt
        ON pt.project_id = p.id AND pt.language = :language
    WHERE p.status = 'published'
    ORDER BY p.created_at DESC
");

$stmt->execute([
    'language' => $lang,
]);

$projects = $stmt->fetchAll();

?>

<main>
    <section class="page-hero">
        <span><?= escape($t['projects']); ?></span>
        <h1><?= escape($t['projects']); ?></h1>
        <p>Uma seleção de projetos autorais, residenciais, comerciais e institucionais.</p>
    </section>

    <section class="projects-list-section">
        <?php if (!$projects): ?>
            <p>Nenhum projeto publicado ainda.</p>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($projects as $project): ?>
                    <article class="card">
                        <?php if ($project['cover_image']): ?>
                            <div 
                                class="card-image"
                                style="background-image: url('<?= BASE_URL . '/' . escape($project['cover_image']); ?>');"
                            ></div>
                        <?php else: ?>
                            <div class="card-image"></div>
                        <?php endif; ?>

                        <h3><?= escape($project['title']); ?></h3>
                        <p><?= escape($project['location']); ?></p>

                        <a 
                            href="<?= BASE_URL; ?>/projeto.php?slug=<?= escape($project['slug']); ?>&lang=<?= escape($lang); ?>" 
                            class="card-link"
                        >
                            Ver projeto →
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>