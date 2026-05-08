<?php

$pageTitle = 'Novo Vídeo';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';

$categoriesStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE type = 'video'
    ORDER BY name ASC
");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $videoUrl = trim($_POST['video_url'] ?? '');
    $status = $_POST['status'] ?? 'draft';

    $ptTitle = trim($_POST['pt_title'] ?? '');
    $ptDescription = trim($_POST['pt_description'] ?? '');

    $enTitle = trim($_POST['en_title'] ?? '');
    $enDescription = trim($_POST['en_description'] ?? '');

    $esTitle = trim($_POST['es_title'] ?? '');
    $esDescription = trim($_POST['es_description'] ?? '');

    if ($videoUrl === '' || $ptTitle === '') {
        $error = 'Informe a URL do vídeo e pelo menos o título em português.';
    } else {
        $thumbnail = uploadFile(
            $_FILES['thumbnail'] ?? [],
            __DIR__ . '/../../uploads/videos'
        );

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO videos (
                video_url,
                thumbnail,
                category_id,
                status
            ) VALUES (
                :video_url,
                :thumbnail,
                :category_id,
                :status
            )
        ");

        $stmt->execute([
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnail,
            'category_id' => $categoryId,
            'status' => $status,
        ]);

        $videoId = (int) $pdo->lastInsertId();

        $translationStmt = $pdo->prepare("
            INSERT INTO video_translations (
                video_id,
                language,
                title,
                slug,
                description
            ) VALUES (
                :video_id,
                :language,
                :title,
                :slug,
                :description
            )
        ");

        $translations = [
            [
                'language' => 'pt',
                'title' => $ptTitle,
                'description' => $ptDescription,
            ],
            [
                'language' => 'en',
                'title' => $enTitle ?: $ptTitle,
                'description' => $enDescription ?: $ptDescription,
            ],
            [
                'language' => 'es',
                'title' => $esTitle ?: $ptTitle,
                'description' => $esDescription ?: $ptDescription,
            ],
        ];

        foreach ($translations as $translation) {
            $translationStmt->execute([
                'video_id' => $videoId,
                'language' => $translation['language'],
                'title' => $translation['title'],
                'slug' => slugify($translation['title']),
                'description' => $translation['description'],
            ]);
        }

        $pdo->commit();

        redirect(BASE_URL . '/admin/videos/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Novo vídeo</h1>

    <div class="admin-panel">
        <?php if ($error): ?>
            <div class="alert-error"><?= escape($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <h2>Dados gerais</h2>

            <label for="video_url">URL do vídeo</label>
            <input
                type="url"
                id="video_url"
                name="video_url"
                placeholder="https://www.youtube.com/watch?v=..."
                required
            >

            <label for="thumbnail">Thumbnail</label>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*">

            <label for="category_id">Categoria</label>
            <select id="category_id" name="category_id">
                <option value="">Sem categoria</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= escape((string) $category['id']); ?>">
                        <?= escape($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="draft">Rascunho</option>
                <option value="published">Publicado</option>
            </select>

            <hr>

            <h2>Português</h2>

            <label for="pt_title">Título</label>
            <input type="text" id="pt_title" name="pt_title" required>

            <label for="pt_description">Descrição</label>
            <textarea id="pt_description" name="pt_description"></textarea>

            <h2>Inglês</h2>

            <label for="en_title">Title</label>
            <input type="text" id="en_title" name="en_title">

            <label for="en_description">Description</label>
            <textarea id="en_description" name="en_description"></textarea>

            <h2>Espanhol</h2>

            <label for="es_title">Título</label>
            <input type="text" id="es_title" name="es_title">

            <label for="es_description">Descripción</label>
            <textarea id="es_description" name="es_description"></textarea>

            <div class="form-actions">
                <button type="submit" class="admin-button">Salvar</button>
                <a href="<?= BASE_URL; ?>/admin/videos/index.php">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>