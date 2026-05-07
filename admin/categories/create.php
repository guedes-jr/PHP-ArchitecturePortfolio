<?php

$pageTitle = 'Nova Categoria';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');

    if ($name === '' || $type === '') {
        $error = 'Preencha todos os campos obrigatórios.';
    } else {
        $slug = slugify($name);

        $stmt = $pdo->prepare("
            INSERT INTO categories (
                name,
                slug,
                type
            ) VALUES (
                :name,
                :slug,
                :type
            )
        ");

        $stmt->execute([
            'name' => $name,
            'slug' => $slug,
            'type' => $type,
        ]);

        redirect(BASE_URL . '/admin/categories/index.php');
    }
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <h1>Nova categoria</h1>

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
                required
            >

            <label for="type">Tipo</label>
            <select id="type" name="type" required>
                <option value="">Selecione</option>
                <option value="project">Projeto</option>
                <option value="article">Artigo</option>
                <option value="video">Vídeo</option>
                <option value="slide">Slide/Publicação</option>
                <option value="gallery">Galeria</option>
            </select>

            <div class="form-actions">
                <button type="submit" class="admin-button">
                    Salvar
                </button>

                <a href="<?= BASE_URL; ?>/admin/categories/index.php">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>