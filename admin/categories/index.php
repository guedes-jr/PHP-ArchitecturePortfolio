<?php

$pageTitle = 'Categorias';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY created_at DESC
");

$categories = $stmt->fetchAll();

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Categorias</h1>
            <p>Gerencie as categorias usadas em projetos, artigos, vídeos e slides.</p>
        </div>

        <a href="<?= BASE_URL; ?>/admin/categories/create.php" class="admin-button">
            Nova categoria
        </a>
    </div>

    <div class="admin-panel">
        <?php if (!$categories): ?>
            <p>Nenhuma categoria cadastrada ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Tipo</th>
                        <th>Criada em</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= escape($category['name']); ?></td>
                            <td><?= escape($category['slug']); ?></td>
                            <td><?= escape($category['type']); ?></td>
                            <td><?= escape(date('d/m/Y H:i', strtotime($category['created_at']))); ?></td>
                            <td>
                                <a href="<?= BASE_URL; ?>/admin/categories/edit.php?id=<?= escape((string) $category['id']); ?>">
                                    Editar
                                </a>
                                |
                                <a 
                                    href="<?= BASE_URL; ?>/admin/categories/delete.php?id=<?= escape((string) $category['id']); ?>"
                                    onclick="return confirm('Deseja excluir esta categoria?')"
                                >
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>