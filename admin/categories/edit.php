<?php

$pageTitle = 'Editar Categoria';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
$stmt->execute([
    'id' => $id,
]);

$category = $stmt->fetch();

if (!$category) {
    redirect(BASE_URL . '/admin/categories/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');

    if ($name === '' || $type === '') {
        $error = 'Preencha todos os campos obrigatórios.';
    } else {
        $slug = slugify($name);

        $updateStmt = $pdo->prepare("
            UPDATE categories
            SET
                name = :name,
                slug = :slug,
                type = :type
            WHERE id = :id
        ");

        $updateStmt->execute([
            'name' => $name,
            'slug' => $slug,
            'type' => $type,
            'id' => $id,
        ]);

        redirect(BASE_URL . '/admin/categories/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Editar categoria</h1>

    <div class="admin-panel">
        <?php if ($error): ?>
            <div class="alert-error">
                <?= escape($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <label for="name">Nome</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?= escape($category['name']); ?>"
                required
            >

            <label for="type">Tipo</label>
            <select id="type" name="type" required>
                <option value="">Selecione</option>
                <option value="project" <?= $category['type'] === 'project' ? 'selected' : ''; ?>>Projeto</option>
                <option value="article" <?= $category['type'] === 'article' ? 'selected' : ''; ?>>Artigo</option>
                <option value="video" <?= $category['type'] === 'video' ? 'selected' : ''; ?>>Vídeo</option>
                <option value="slide" <?= $category['type'] === 'slide' ? 'selected' : ''; ?>>Slide/Publicação</option>
                <option value="gallery" <?= $category['type'] === 'gallery' ? 'selected' : ''; ?>>Galeria</option>
            </select>

            <div class="form-actions">
                <button type="submit" class="admin-button">
                    Atualizar
                </button>

                <a href="<?= BASE_URL; ?>/admin/categories/index.php">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>