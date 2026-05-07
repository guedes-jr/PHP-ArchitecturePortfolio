<?php

$pageTitle = 'Projetos';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        p.id,
        p.cover_image,
        p.status,
        p.is_featured,
        p.created_at,
        c.name AS category_name,
        pt.title,
        pt.location
    FROM projects p
    LEFT JOIN categories c ON c.id = p.category_id
    LEFT JOIN project_translations pt 
        ON pt.project_id = p.id AND pt.language = 'pt'
    ORDER BY p.created_at DESC
");

$projects = $stmt->fetchAll();

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Projetos</h1>
            <p>Gerencie os projetos exibidos no site.</p>
        </div>

        <a href="<?= BASE_URL; ?>/admin/projects/create.php" class="admin-button">
            Novo projeto
        </a>
    </div>

    <div class="admin-panel">
        <?php if (!$projects): ?>
            <p>Nenhum projeto cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Destaque</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <?php if ($project['cover_image']): ?>
                                    <img 
                                        src="<?= BASE_URL . '/' . escape($project['cover_image']); ?>" 
                                        class="admin-thumb"
                                        alt=""
                                    >
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= escape($project['title'] ?? 'Sem título'); ?>
                                <br>
                                <small><?= escape($project['location'] ?? ''); ?></small>
                            </td>

                            <td><?= escape($project['category_name'] ?? '-'); ?></td>
                            <td><?= escape($project['status']); ?></td>
                            <td><?= $project['is_featured'] ? 'Sim' : 'Não'; ?></td>
                            <td><?= escape(date('d/m/Y H:i', strtotime($project['created_at']))); ?></td>

                            <td>
                                <a href="<?= BASE_URL; ?>/admin/projects/edit.php?id=<?= escape((string) $project['id']); ?>">
                                    Editar
                                </a>
                                |
                                <a 
                                    href="<?= BASE_URL; ?>/admin/projects/delete.php?id=<?= escape((string) $project['id']); ?>"
                                    onclick="return confirm('Deseja excluir este projeto?')"
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