<?php

$pageTitle = 'Artigos';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT
        a.id,
        a.cover_image,
        a.author,
        a.status,
        a.views,
        a.created_at,
        c.name AS category_name,
        at.title
    FROM articles a
    LEFT JOIN categories c ON c.id = a.category_id
    LEFT JOIN article_translations at
        ON at.article_id = a.id AND at.language = 'pt'
    ORDER BY a.created_at DESC
");

$articles = $stmt->fetchAll();

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Artigos</h1>
            <p>Gerencie publicações, textos, notícias e conteúdos editoriais do site.</p>
        </div>

        <a href="<?= BASE_URL; ?>/admin/articles/create.php" class="admin-button">
            Novo artigo
        </a>
    </div>

    <div class="admin-panel">
        <?php if (!$articles): ?>
            <p>Nenhum artigo cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Autor</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr>
                            <td>
                                <?php if ($article['cover_image']): ?>
                                    <img
                                        src="<?= BASE_URL . '/' . escape($article['cover_image']); ?>"
                                        class="admin-thumb"
                                        alt=""
                                    >
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td><?= escape($article['title'] ?? 'Sem título'); ?></td>
                            <td><?= escape($article['category_name'] ?? '-'); ?></td>
                            <td><?= escape($article['author'] ?? '-'); ?></td>
                            <td><?= escape($article['status']); ?></td>
                            <td><?= escape((string) $article['views']); ?></td>
                            <td><?= escape(date('d/m/Y H:i', strtotime($article['created_at']))); ?></td>

                            <td>
                                <a href="<?= BASE_URL; ?>/admin/articles/edit.php?id=<?= escape((string) $article['id']); ?>">
                                    Editar
                                </a>
                                |
                                <a
                                    href="<?= BASE_URL; ?>/admin/articles/delete.php?id=<?= escape((string) $article['id']); ?>"
                                    onclick="return confirm('Deseja excluir este artigo?')"
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