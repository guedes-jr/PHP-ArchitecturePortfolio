<?php

$pageTitle = 'Novo Projeto';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';

$categoriesStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE type = 'project'
    ORDER BY name ASC
");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

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
        $coverImage = uploadFile(
            $_FILES['cover_image'] ?? [],
            __DIR__ . '/../../uploads/projects'
        );

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO projects (
                cover_image,
                category_id,
                is_featured,
                status
            ) VALUES (
                :cover_image,
                :category_id,
                :is_featured,
                :status
            )
        ");

        $stmt->execute([
            'cover_image' => $coverImage,
            'category_id' => $categoryId,
            'is_featured' => $isFeatured,
            'status' => $status,
        ]);

        $projectId = (int) $pdo->lastInsertId();

        $translationStmt = $pdo->prepare("
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

        $translations = [
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

        foreach ($translations as $translation) {
            $translationStmt->execute([
                'project_id' => $projectId,
                'language' => $translation['language'],
                'title' => $translation['title'],
                'slug' => slugify($translation['title']),
                'location' => $translation['location'],
                'description' => $translation['description'],
            ]);
        }

        $pdo->commit();

        redirect(BASE_URL . '/admin/projects/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Novo projeto</h1>

    <div class="admin-panel">
        <?php if ($error): ?>
            <div class="alert-error"><?= escape($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <h2>Dados gerais</h2>

            <label for="cover_image">Imagem de capa</label>
            <input type="file" id="cover_image" name="cover_image" accept="image/*">

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

            <label class="checkbox-label">
                <input type="checkbox" name="is_featured" value="1">
                Exibir como destaque na home
            </label>

            <hr>

            <h2>Português</h2>

            <label for="pt_title">Título</label>
            <input type="text" id="pt_title" name="pt_title" required>

            <label for="pt_location">Localização</label>
            <input type="text" id="pt_location" name="pt_location">

            <label for="pt_description">Descrição</label>
            <textarea id="pt_description" name="pt_description"></textarea>

            <h2>Inglês</h2>

            <label for="en_title">Title</label>
            <input type="text" id="en_title" name="en_title">

            <label for="en_location">Location</label>
            <input type="text" id="en_location" name="en_location">

            <label for="en_description">Description</label>
            <textarea id="en_description" name="en_description"></textarea>

            <h2>Espanhol</h2>

            <label for="es_title">Título</label>
            <input type="text" id="es_title" name="es_title">

            <label for="es_location">Ubicación</label>
            <input type="text" id="es_location" name="es_location">

            <label for="es_description">Descripción</label>
            <textarea id="es_description" name="es_description"></textarea>

            <div class="form-actions">
                <button type="submit" class="admin-button">Salvar</button>
                <a href="<?= BASE_URL; ?>/admin/projects/index.php">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>