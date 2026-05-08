<?php

$pageTitle = 'Editar Vídeo';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM videos WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$video = $stmt->fetch();

if (!$video) {
    redirect(BASE_URL . '/admin/videos/index.php');
}

$categoriesStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE type = 'video'
    ORDER BY name ASC
");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

$translationsStmt = $pdo->prepare("
    SELECT *
    FROM video_translations
    WHERE video_id = :video_id
");
$translationsStmt->execute(['video_id' => $id]);

$translations = [];

foreach ($translationsStmt->fetchAll() as $translation) {
    $translations[$translation['language']] = $translation;
}

$error = '';

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
        $thumbnail = $video['thumbnail'];

        $newThumbnail = uploadFile(
            $_FILES['thumbnail'] ?? [],
            __DIR__ . '/../../uploads/videos'
        );

        if ($newThumbnail !== null) {
            if ($thumbnail) {
                $oldImagePath = __DIR__ . '/../../' . $thumbnail;

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $thumbnail = $newThumbnail;
        }

        $pdo->beginTransaction();

        $updateVideoStmt = $pdo->prepare("
            UPDATE videos
            SET
                video_url = :video_url,
                thumbnail = :thumbnail,
                category_id = :category_id,
                status = :status
            WHERE id = :id
        ");

        $updateVideoStmt->execute([
            'video_url' => $videoUrl,
            'thumbnail' => $thumbnail,
            'category_id' => $categoryId,
            'status' => $status,
            'id' => $id,
        ]);

        $deleteTranslationsStmt = $pdo->prepare("
            DELETE FROM video_translations
            WHERE video_id = :video_id
        ");
        $deleteTranslationsStmt->execute(['video_id' => $id]);

        $insertTranslationStmt = $pdo->prepare("
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

        $items = [
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

        foreach ($items as $item) {
            $insertTranslationStmt->execute([
                'video_id' => $id,
                'language' => $item['language'],
                'title' => $item['title'],
                'slug' => slugify($item['title']),
                'description' => $item['description'],
            ]);
        }

        $pdo->commit();

        redirect(BASE_URL . '/admin/videos/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Editar vídeo</h1>

    <div class="admin-panel">
        <?php if ($error): ?>
            <div class="alert-error"><?= escape($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <h2>Dados gerais</h2>

            <?php if ($video['thumbnail']): ?>
                <img
                    src="<?= BASE_URL . '/' . escape($video['thumbnail']); ?>"
                    class="admin-preview-image"
                    alt=""
                >
            <?php endif; ?>

            <label for="video_url">URL do vídeo</label>
            <input
                type="url"
                id="video_url"
                name="video_url"
                value="<?= escape($video['video_url']); ?>"
                required
            >

            <label for="thumbnail">Alterar thumbnail</label>
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*">

            <label for="category_id">Categoria</label>
            <select id="category_id" name="category_id">
                <option value="">Sem categoria</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= escape((string) $category['id']); ?>"
                        <?= (int) $video['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>
                    >
                        <?= escape($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="draft" <?= $video['status'] === 'draft' ? 'selected' : ''; ?>>
                    Rascunho
                </option>
                <option value="published" <?= $video['status'] === 'published' ? 'selected' : ''; ?>>
                    Publicado
                </option>
            </select>

            <hr>

            <h2>Português</h2>

            <label for="pt_title">Título</label>
            <input
                type="text"
                id="pt_title"
                name="pt_title"
                value="<?= escape($translations['pt']['title'] ?? ''); ?>"
                required
            >

            <label for="pt_description">Descrição</label>
            <textarea id="pt_description" name="pt_description"><?= escape($translations['pt']['description'] ?? ''); ?></textarea>

            <h2>Inglês</h2>

            <label for="en_title">Title</label>
            <input
                type="text"
                id="en_title"
                name="en_title"
                value="<?= escape($translations['en']['title'] ?? ''); ?>"
            >

            <label for="en_description">Description</label>
            <textarea id="en_description" name="en_description"><?= escape($translations['en']['description'] ?? ''); ?></textarea>

            <h2>Espanhol</h2>

            <label for="es_title">Título</label>
            <input
                type="text"
                id="es_title"
                name="es_title"
                value="<?= escape($translations['es']['title'] ?? ''); ?>"
            >

            <label for="es_description">Descripción</label>
            <textarea id="es_description" name="es_description"><?= escape($translations['es']['description'] ?? ''); ?></textarea>

            <div class="form-actions">
                <button type="submit" class="admin-button">Atualizar</button>
                <a href="<?= BASE_URL; ?>/admin/videos/index.php">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>