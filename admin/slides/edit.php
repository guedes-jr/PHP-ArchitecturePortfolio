<?php

$pageTitle = 'Editar Publicação';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM slides WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$slide = $stmt->fetch();

if (!$slide) {
    redirect(BASE_URL . '/admin/slides/index.php');
}

$categoriesStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE type = 'slide'
    ORDER BY name ASC
");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

$translationsStmt = $pdo->prepare("
    SELECT *
    FROM slide_translations
    WHERE slide_id = :slide_id
");
$translationsStmt->execute(['slide_id' => $id]);

$translations = [];

foreach ($translationsStmt->fetchAll() as $translation) {
    $translations[$translation['language']] = $translation;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $status = $_POST['status'] ?? 'draft';

    $ptTitle = trim($_POST['pt_title'] ?? '');
    $ptDescription = trim($_POST['pt_description'] ?? '');

    $enTitle = trim($_POST['en_title'] ?? '');
    $enDescription = trim($_POST['en_description'] ?? '');

    $esTitle = trim($_POST['es_title'] ?? '');
    $esDescription = trim($_POST['es_description'] ?? '');

    if ($ptTitle === '') {
        $error = 'Informe pelo menos o título em português.';
    } else {
        $filePath = $slide['file_path'];
        $coverImage = $slide['cover_image'];

        $newFilePath = uploadDocument(
            $_FILES['file_path'] ?? [],
            __DIR__ . '/../../uploads/slides'
        );

        if ($newFilePath !== null) {
            if ($filePath) {
                $oldFilePath = __DIR__ . '/../../' . $filePath;

                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $filePath = $newFilePath;
        }

        $newCoverImage = uploadFile(
            $_FILES['cover_image'] ?? [],
            __DIR__ . '/../../uploads/slides'
        );

        if ($newCoverImage !== null) {
            if ($coverImage) {
                $oldCoverPath = __DIR__ . '/../../' . $coverImage;

                if (file_exists($oldCoverPath)) {
                    unlink($oldCoverPath);
                }
            }

            $coverImage = $newCoverImage;
        }

        $pdo->beginTransaction();

        $updateSlideStmt = $pdo->prepare("
            UPDATE slides
            SET
                file_path = :file_path,
                cover_image = :cover_image,
                category_id = :category_id,
                status = :status
            WHERE id = :id
        ");

        $updateSlideStmt->execute([
            'file_path' => $filePath,
            'cover_image' => $coverImage,
            'category_id' => $categoryId,
            'status' => $status,
            'id' => $id,
        ]);

        $deleteTranslationsStmt = $pdo->prepare("
            DELETE FROM slide_translations
            WHERE slide_id = :slide_id
        ");
        $deleteTranslationsStmt->execute(['slide_id' => $id]);

        $insertTranslationStmt = $pdo->prepare("
            INSERT INTO slide_translations (
                slide_id,
                language,
                title,
                slug,
                description
            ) VALUES (
                :slide_id,
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
                'slide_id' => $id,
                'language' => $item['language'],
                'title' => $item['title'],
                'slug' => slugify($item['title']),
                'description' => $item['description'],
            ]);
        }

        $pdo->commit();

        redirect(BASE_URL . '/admin/slides/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Editar publicação</h1>

    <div class="admin-panel">
        <?php if ($error): ?>
            <div class="alert-error"><?= escape($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <h2>Dados gerais</h2>

            <p>
                <strong>Arquivo atual:</strong>
                <a href="<?= BASE_URL . '/' . escape($slide['file_path']); ?>" target="_blank">
                    Abrir arquivo
                </a>
            </p>

            <label for="file_path">Alterar arquivo PDF/PPT/PPTX</label>
            <input type="file" id="file_path" name="file_path" accept=".pdf,.ppt,.pptx">

            <?php if ($slide['cover_image']): ?>
                <img
                    src="<?= BASE_URL . '/' . escape($slide['cover_image']); ?>"
                    class="admin-preview-image"
                    alt=""
                >
            <?php endif; ?>

            <label for="cover_image">Alterar imagem de capa</label>
            <input type="file" id="cover_image" name="cover_image" accept="image/*">

            <label for="category_id">Categoria</label>
            <select id="category_id" name="category_id">
                <option value="">Sem categoria</option>

                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?= escape((string) $category['id']); ?>"
                        <?= (int) $slide['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>
                    >
                        <?= escape($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="draft" <?= $slide['status'] === 'draft' ? 'selected' : ''; ?>>
                    Rascunho
                </option>
                <option value="published" <?= $slide['status'] === 'published' ? 'selected' : ''; ?>>
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
                <a href="<?= BASE_URL; ?>/admin/slides/index.php">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>