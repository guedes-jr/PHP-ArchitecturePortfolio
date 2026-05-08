<?php

$pageTitle = 'Vídeos';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        v.id,
        v.video_url,
        v.thumbnail,
        v.status,
        v.views,
        v.created_at,
        c.name AS category_name,
        vt.title
    FROM videos v
    LEFT JOIN categories c ON c.id = v.category_id
    LEFT JOIN video_translations vt
        ON vt.video_id = v.id AND vt.language = 'pt'
    ORDER BY v.created_at DESC
");

$videos = $stmt->fetchAll();

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Vídeos</h1>
            <p>Gerencie vídeos, entrevistas, apresentações e conteúdos audiovisuais.</p>
        </div>

        <a href="<?= BASE_URL; ?>/admin/videos/create.php" class="admin-button">
            Novo vídeo
        </a>
    </div>

    <div class="admin-panel">
        <?php if (!$videos): ?>
            <p>Nenhum vídeo cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($videos as $video): ?>
                        <tr>
                            <td>
                                <?php if ($video['thumbnail']): ?>
                                    <img
                                        src="<?= BASE_URL . '/' . escape($video['thumbnail']); ?>"
                                        class="admin-thumb"
                                        alt=""
                                    >
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= escape($video['title'] ?? 'Sem título'); ?>
                                <br>
                                <small>
                                    <a href="<?= escape($video['video_url']); ?>" target="_blank">
                                        Abrir vídeo
                                    </a>
                                </small>
                            </td>

                            <td><?= escape($video['category_name'] ?? '-'); ?></td>
                            <td><?= escape($video['status']); ?></td>
                            <td><?= escape((string) $video['views']); ?></td>
                            <td><?= escape(date('d/m/Y H:i', strtotime($video['created_at']))); ?></td>

                            <td>
                                <a href="<?= BASE_URL; ?>/admin/videos/edit.php?id=<?= escape((string) $video['id']); ?>">
                                    Editar
                                </a>
                                |
                                <a
                                    href="<?= BASE_URL; ?>/admin/videos/delete.php?id=<?= escape((string) $video['id']); ?>"
                                    onclick="return confirm('Deseja excluir este vídeo?')"
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