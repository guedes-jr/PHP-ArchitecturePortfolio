<?php

$pageTitle = 'Editar Projeto';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$project = $stmt->fetch();

if (!$project) {
    redirect(BASE_URL . '/admin/projects/index.php');
}

$categoriesStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE type = 'project'
    ORDER BY name ASC
");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

$translationsStmt = $pdo->prepare("
    SELECT *
    FROM project_translations
    WHERE project_id = :project_id
");
$translationsStmt->execute(['project_id' => $id]);

$translations = [];

foreach ($translationsStmt->fetchAll() as $translation) {
    $translations[$translation['language']] = $translation;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $status = $_POST['status'] ?? 'draft';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    $ptTitle = trim($_POST['pt_title'] ?? '');
    $ptLocation = trim($_POST['pt_location'] ?? '');
    $ptDescription = trim($_POST['pt_description'] ?? '');

    $enTitle = trim($_POST['en_title'] ?? '');
    $enLocation = trim($_POST['en_location'] ?? '');
    $enDescription = trim($_POST['en_description'] ?? '');

    $esTitle = trim($_POST['es_title'] ?? '');
    $esLocation = trim($_POST['es_location'] ?? '');
    $esDescription = trim($_POST['es_description'] ?? '');

    if ($ptTitle === '') {
        $error = 'Informe pelo menos o título em português.';
    } else {
        $coverImage = $project['cover_image'];

        $newCoverImage = uploadFile(
            $_FILES['cover_image'] ?? [],
            __DIR__ . '/../../uploads/projects'
        );

        if ($newCoverImage !== null) {
            if ($coverImage) {
                $oldImagePath = __DIR__ . '/../../' . $coverImage;

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $coverImage = $newCoverImage;
        }

        $pdo->beginTransaction();

        $updateProjectStmt = $pdo->prepare("
            UPDATE projects
            SET
                cover_image = :cover_image,
                category_id = :category_id,
                is_featured = :is_featured,
                status = :status
            WHERE id = :id
        ");

        $updateProjectStmt->execute([
            'cover_image' => $coverImage,
            'category_id' => $categoryId,
            'is_featured' => $isFeatured,
            'status' => $status,
            'id' => $id,
        ]);

        $deleteTranslationsStmt = $pdo->prepare("
            DELETE FROM project_translations
            WHERE project_id = :project_id
        ");
        $deleteTranslationsStmt->execute(['project_id' => $id]);

        $insertTranslationStmt = $pdo->prepare("
            INSERT INTO project_translations (
                project_id,
                language,
                title,
                slug,
                location,
                description
            ) VALUES (
                :project_id,
                :language,
                :title,
                :slug,
                :location,
                :description
            )
        ");

        $items = [
            [
                'language' => 'pt',
                'title' => $ptTitle,
                'location' => $ptLocation,
                'description' => $ptDescription,
            ],
            [
                'language' => 'en',
                'title' => $enTitle ?: $ptTitle,
                'location' => $enLocation ?: $ptLocation,
                'description' => $enDescription ?: $ptDescription,
            ],
            [
                'language' => 'es',
                'title' => $esTitle ?: $ptTitle,
                'location' => $esLocation ?: $ptLocation,
                'description' => $esDescription ?: $ptDescription,
            ],
        ];

        foreach ($items as $item) {
            $insertTranslationStmt->execute([
                'project_id' => $id,
                'language' => $item['language'],
                'title' => $item['title'],
                'slug' => slugify($item['title']),
                'location' => $item['location'],
                'description' => $item['description'],
            ]);
        }

        $pdo->commit();

        redirect(BASE_URL . '/admin/projects/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Editar projeto</h1>

    <div class="admin-panel">
        <?php if ($error): ?>
            <div class="alert-error"><?= escape($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <h2>Dados gerais</h2>

            <?php if ($project['cover_image']): ?>
                <img 
                    src="<?= BASE_URL . '/' . escape($project['cover_image']); ?>" 
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
                        <?= (int) $project['category_id'] === (int) $category['id'] ? 'selected' : ''; ?>
                    >
                        <?= escape($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="draft" <?= $project['status'] === 'draft' ? 'selected' : ''; ?>>
                    Rascunho
                </option>
                <option value="published" <?= $project['status'] === 'published' ? 'selected' : ''; ?>>
                    Publicado
                </option>
            </select>

            <label class="checkbox-label">
                <input 
                    type="checkbox" 
                    name="is_featured" 
                    value="1"
                    <?= (int) $project['is_featured'] === 1 ? 'checked' : ''; ?>
                >
                Exibir como destaque na home
            </label>

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

            <label for="pt_location">Localização</label>
            <input 
                type="text" 
                id="pt_location" 
                name="pt_location"
                value="<?= escape($translations['pt']['location'] ?? ''); ?>"
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

            <label for="en_location">Location</label>
            <input 
                type="text" 
                id="en_location" 
                name="en_location"
                value="<?= escape($translations['en']['location'] ?? ''); ?>"
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

            <label for="es_location">Ubicación</label>
            <input 
                type="text" 
                id="es_location" 
                name="es_location"
                value="<?= escape($translations['es']['location'] ?? ''); ?>"
            >

            <label for="es_description">Descripción</label>
            <textarea id="es_description" name="es_description"><?= escape($translations['es']['description'] ?? ''); ?></textarea>

            <div class="form-actions">
                <button type="submit" class="admin-button">Atualizar</button>
                <a href="<?= BASE_URL; ?>/admin/projects/index.php">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>