<?php

$pageTitle = 'Slides e Publicações';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        s.id,
        s.file_path,
        s.cover_image,
        s.status,
        s.downloads,
        s.created_at,
        c.name AS category_name,
        st.title
    FROM slides s
    LEFT JOIN categories c ON c.id = s.category_id
    LEFT JOIN slide_translations st
        ON st.slide_id = s.id AND st.language = 'pt'
    ORDER BY s.created_at DESC
");

$slides = $stmt->fetchAll();

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Slides e Publicações</h1>
            <p>Gerencie PDFs, apresentações, portfólios e materiais institucionais.</p>
        </div>

        <a href="<?= BASE_URL; ?>/admin/slides/create.php" class="admin-button">
            Nova publicação
        </a>
    </div>

    <div class="admin-panel">
        <?php if (!$slides): ?>
            <p>Nenhum material cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Capa</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Downloads</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($slides as $slide): ?>
                        <tr>
                            <td>
                                <?php if ($slide['cover_image']): ?>
                                    <img
                                        src="<?= BASE_URL . '/' . escape($slide['cover_image']); ?>"
                                        class="admin-thumb"
                                        alt=""
                                    >
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= escape($slide['title'] ?? 'Sem título'); ?>
                                <br>
                                <small>
                                    <a href="<?= BASE_URL . '/' . escape($slide['file_path']); ?>" target="_blank">
                                        Abrir arquivo
                                    </a>
                                </small>
                            </td>

                            <td><?= escape($slide['category_name'] ?? '-'); ?></td>
                            <td><?= escape($slide['status']); ?></td>
                            <td><?= escape((string) $slide['downloads']); ?></td>
                            <td><?= escape(date('d/m/Y H:i', strtotime($slide['created_at']))); ?></td>

                            <td>
                                <a href="<?= BASE_URL; ?>/admin/slides/edit.php?id=<?= escape((string) $slide['id']); ?>">
                                    Editar
                                </a>
                                |
                                <a
                                    href="<?= BASE_URL; ?>/admin/slides/delete.php?id=<?= escape((string) $slide['id']); ?>"
                                    onclick="return confirm('Deseja excluir este material?')"
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