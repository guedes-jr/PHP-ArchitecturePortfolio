<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$siteName = getSetting($pdo, 'site_name', $lang);
$heroEyebrow = getSetting($pdo, 'hero_eyebrow', $lang);
$heroTitle = getSetting($pdo, 'hero_title', $lang);
$heroDescription = getSetting($pdo, 'hero_description', $lang);
$aboutTitle = getSetting($pdo, 'about_title', $lang);
$aboutText = getSetting($pdo, 'about_text', $lang);

$featuredProjectsStmt = $pdo->prepare("
    SELECT
        p.id,
        p.cover_image,
        pt.title,
        pt.slug,
        pt.location
    FROM projects p
    INNER JOIN project_translations pt 
        ON pt.project_id = p.id AND pt.language = :language
    WHERE p.status = 'published'
      AND p.is_featured = 1
    ORDER BY p.created_at DESC
    LIMIT 3
");

$featuredProjectsStmt->execute([
    'language' => $lang,
]);

$featuredProjects = $featuredProjectsStmt->fetchAll();

?>

<main>
    <section class="hero">
        <div class="hero-content">
            <p class="eyebrow"><?= escape($heroEyebrow); ?></p>
            <h1><?= escape($heroTitle); ?></h1>
            <p>
                <?= escape($heroDescription); ?>
            </p>

            <div class="hero-actions">
                <a href="<?= BASE_URL; ?>/projetos.php?lang=<?= escape($lang); ?>" class="btn-primary">
                    <?= escape($t['explore_projects']); ?>
                </a>

                <a href="#about" class="btn-secondary">
                    <?= escape($t['view_profile']); ?>
                </a>
            </div>
        </div>
    </section>

    <section class="section-grid">
        <div class="section-title">
            <span>Featured Projects</span>
            <h2>Architecture for a Better Future</h2>
            <a href="<?= BASE_URL; ?>/projetos.php?lang=<?= escape($lang); ?>">View all projects →</a>
        </div>

        <div class="cards-grid">
            <?php if (!$featuredProjects): ?>
                <p>Nenhum projeto em destaque cadastrado ainda.</p>
            <?php else: ?>
                <?php foreach ($featuredProjects as $project): ?>
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
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section id="about" class="about-section">
        <div class="about-image"></div>

        <div class="about-content">
            <span><?= escape($aboutTitle); ?></span>
            <h2><?= escape($siteName); ?></h2>
            <p>
                <?= escape($aboutText); ?>
            </p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>