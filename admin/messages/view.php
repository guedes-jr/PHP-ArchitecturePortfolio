<?php

$pageTitle = 'Ver Mensagem';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM contact_messages
    WHERE id = :id
    LIMIT 1
");

$stmt->execute(['id' => $id]);
$message = $stmt->fetch();

if (!$message) {
    redirect(BASE_URL . '/admin/messages/index.php');
}

$updateStmt = $pdo->prepare("
    UPDATE contact_messages
    SET status = 'read'
    WHERE id = :id
");

$updateStmt->execute(['id' => $id]);

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Mensagem recebida</h1>
            <p>Mensagem enviada pelo formulário de contato.</p>
        </div>

        <a href="<?= BASE_URL; ?>/admin/messages/index.php" class="admin-button">
            Voltar
        </a>
    </div>

    <div class="admin-panel">
        <p>
            <strong>Nome:</strong><br>
            <?= escape($message['name']); ?>
        </p>

        <p>
            <strong>E-mail:</strong><br>
            <?= escape($message['email']); ?>
        </p>

        <p>
            <strong>Telefone:</strong><br>
            <?= escape($message['phone'] ?: '-'); ?>
        </p>

        <p>
            <strong>Assunto:</strong><br>
            <?= escape($message['subject'] ?: '-'); ?>
        </p>

        <p>
            <strong>Recebida em:</strong><br>
            <?= escape(date('d/m/Y H:i', strtotime($message['created_at']))); ?>
        </p>

        <hr>

        <p>
            <strong>Mensagem:</strong>
        </p>

        <div class="message-box">
            <?= nl2br(escape($message['message'])); ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>