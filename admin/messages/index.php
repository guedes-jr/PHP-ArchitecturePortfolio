<?php

$pageTitle = 'Mensagens';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT *
    FROM contact_messages
    ORDER BY created_at DESC
");

$messages = $stmt->fetchAll();

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Mensagens</h1>
            <p>Visualize mensagens enviadas pelo formulário de contato do site.</p>
        </div>
    </div>

    <div class="admin-panel">
        <?php if (!$messages): ?>
            <p>Nenhuma mensagem recebida ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Assunto</th>
                        <th>Recebida em</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td>
                                <?php if ($message['status'] === 'unread'): ?>
                                    <span class="status-badge unread">Não lida</span>
                                <?php else: ?>
                                    <span class="status-badge read">Lida</span>
                                <?php endif; ?>
                            </td>

                            <td><?= escape($message['name']); ?></td>
                            <td><?= escape($message['email']); ?></td>
                            <td><?= escape($message['subject'] ?: '-'); ?></td>
                            <td><?= escape(date('d/m/Y H:i', strtotime($message['created_at']))); ?></td>

                            <td>
                                <a href="<?= BASE_URL; ?>/admin/messages/view.php?id=<?= escape((string) $message['id']); ?>">
                                    Ver
                                </a>
                                |
                                <a
                                    href="<?= BASE_URL; ?>/admin/messages/delete.php?id=<?= escape((string) $message['id']); ?>"
                                    onclick="return confirm('Deseja excluir esta mensagem?')"
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