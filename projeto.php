<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    redirect(BASE_URL . '/projetos.php?lang=' . $lang);
}

$stmt = $pdo->prepare("
    SELECT
        p.id,
        p.cover_image,
        p.views,
        pt.title,
        pt.location,
        pt.description
    FROM projects p
    INNER JOIN project_translations pt
        ON pt.project_id = p.id AND pt.language = :language
    WHERE p.status = 'published'
      AND pt.slug = :slug
    LIMIT 1
");

$stmt->execute([
    'language' => $lang,
    'slug' => $slug,
]);

$project = $stmt->fetch();

if (!$project) {
    redirect(BASE_URL . '/projetos.php?lang=' . $lang);
}

$updateViewsStmt = $pdo->prepare("
    UPDATE projects
    SET views = views + 1
    WHERE id = :id
");
$updateViewsStmt->execute([
    'id' => $project['id'],
]);

?>

<main>
    <section 
        class="project-detail-hero"
        <?php if ($project['cover_image']): ?>
            style="background-image: url('<?= BASE_URL . '/' . escape($project['cover_image']); ?>');"
        <?php endif; ?>
    >
        <div>
            <span><?= escape($project['location']); ?></span>
            <h1><?= escape($project['title']); ?></h1>
        </div>
    </section>

    <section class="project-detail-content">
        <h2>Sobre o projeto</h2>
        <p><?= nl2br(escape($project['description'])); ?></p>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>