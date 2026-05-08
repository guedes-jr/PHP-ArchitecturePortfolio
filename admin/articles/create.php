<?php

$pageTitle = 'Novo Artigo';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';

$categoriesStmt = $pdo->prepare("
    SELECT *
    FROM categories
    WHERE type = 'article'
    ORDER BY name ASC
");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $author = trim($_POST['author'] ?? '');
    $status = $_POST['status'] ?? 'draft';

    $ptTitle = trim($_POST['pt_title'] ?? '');
    $ptSummary = trim($_POST['pt_summary'] ?? '');
    $ptContent = trim($_POST['pt_content'] ?? '');

    $enTitle = trim($_POST['en_title'] ?? '');
    $enSummary = trim($_POST['en_summary'] ?? '');
    $enContent = trim($_POST['en_content'] ?? '');

    $esTitle = trim($_POST['es_title'] ?? '');
    $esSummary = trim($_POST['es_summary'] ?? '');
    $esContent = trim($_POST['es_content'] ?? '');

    if ($ptTitle === '') {
        $error = 'Informe pelo menos o título em português.';
    } else {
        $coverImage = uploadFile(
            $_FILES['cover_image'] ?? [],
            __DIR__ . '/../../uploads/articles'
        );

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO articles (
                cover_image,
                category_id,
                author,
                status
            ) VALUES (
                :cover_image,
                :category_id,
                :author,
                :status
            )
        ");

        $stmt->execute([
            'cover_image' => $coverImage,
            'category_id' => $categoryId,
            'author' => $author,
            'status' => $status,
        ]);

        $articleId = (int) $pdo->lastInsertId();

        $translationStmt = $pdo->prepare("
            INSERT INTO article_translations (
                article_id,
                language,
                title,
                slug,
                summary,
                content,
                meta_title,
                meta_description
            ) VALUES (
                :article_id,
                :language,
                :title,
                :slug,
                :summary,
                :content,
                :meta_title,
                :meta_description
            )
        ");

        $translations = [
            [
                'language' => 'pt',
                'title' => $ptTitle,
                'summary' => $ptSummary,
                'content' => $ptContent,
            ],
            [
                'language' => 'en',
                'title' => $enTitle ?: $ptTitle,
                'summary' => $enSummary ?: $ptSummary,
                'content' => $enContent ?: $ptContent,
            ],
            [
                'language' => 'es',
                'title' => $esTitle ?: $ptTitle,
                'summary' => $esSummary ?: $ptSummary,
                'content' => $esContent ?: $ptContent,
            ],
        ];

        foreach ($translations as $translation) {
            $translationStmt->execute([
                'article_id' => $articleId,
                'language' => $translation['language'],
                'title' => $translation['title'],
                'slug' => slugify($translation['title']),
                'summary' => $translation['summary'],
                'content' => $translation['content'],
                'meta_title' => $translation['title'],
                'meta_description' => $translation['summary'],
            ]);
        }

        $pdo->commit();

        redirect(BASE_URL . '/admin/articles/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Novo artigo</h1>

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

            <label for="author">Autor</label>
            <input type="text" id="author" name="author">

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="draft">Rascunho</option>
                <option value="published">Publicado</option>
            </select>

            <hr>

            <h2>Português</h2>

            <label for="pt_title">Título</label>
            <input type="text" id="pt_title" name="pt_title" required>

            <label for="pt_summary">Resumo</label>
            <textarea id="pt_summary" name="pt_summary"></textarea>

            <label for="pt_content">Conteúdo</label>
            <textarea id="pt_content" name="pt_content" class="large-textarea"></textarea>

            <h2>Inglês</h2>

            <label for="en_title">Title</label>
            <input type="text" id="en_title" name="en_title">

            <label for="en_summary">Summary</label>
            <textarea id="en_summary" name="en_summary"></textarea>

            <label for="en_content">Content</label>
            <textarea id="en_content" name="en_content" class="large-textarea"></textarea>

            <h2>Espanhol</h2>

            <label for="es_title">Título</label>
            <input type="text" id="es_title" name="es_title">

            <label for="es_summary">Resumen</label>
            <textarea id="es_summary" name="es_summary"></textarea>

            <label for="es_content">Contenido</label>
            <textarea id="es_content" name="es_content" class="large-textarea"></textarea>

            <div class="form-actions">
                <button type="submit" class="admin-button">Salvar</button>
                <a href="<?= BASE_URL; ?>/admin/articles/index.php">Cancelar</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>