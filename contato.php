<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

$email = getSetting($pdo, 'email', $lang);
$whatsapp = getSetting($pdo, 'whatsapp', $lang);
$instagram = getSetting($pdo, 'instagram', $lang);
$linkedin = getSetting($pdo, 'linkedin', $lang);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $messageEmail = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $messageEmail === '' || $message === '') {
        $error = 'Preencha nome, e-mail e mensagem.';
    } elseif (!filter_var($messageEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Informe um e-mail válido.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (
                name,
                email,
                phone,
                subject,
                message
            ) VALUES (
                :name,
                :email,
                :phone,
                :subject,
                :message
            )
        ");

        $stmt->execute([
            'name' => $name,
            'email' => $messageEmail,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
        ]);

        $success = 'Mensagem enviada com sucesso. Em breve retornaremos o contato.';

        $_POST = [];
    }
}

?>

<main>
    <section class="page-hero">
        <span><?= escape($t['contact']); ?></span>
        <h1><?= escape($t['contact']); ?></h1>
        <p>Entre em contato para projetos, convites, publicações, parcerias ou colaborações.</p>
    </section>

    <section class="contact-section">
        <div class="contact-info">
            <h2>Informações de contato</h2>

            <?php if ($email): ?>
                <p>
                    <strong>E-mail:</strong><br>
                    <?= escape($email); ?>
                </p>
            <?php endif; ?>

            <?php if ($whatsapp): ?>
                <p>
                    <strong>WhatsApp:</strong><br>
                    <?= escape($whatsapp); ?>
                </p>
            <?php endif; ?>

            <?php if ($instagram): ?>
                <p>
                    <strong>Instagram:</strong><br>
                    <a href="<?= escape($instagram); ?>" target="_blank">
                        <?= escape($instagram); ?>
                    </a>
                </p>
            <?php endif; ?>

            <?php if ($linkedin): ?>
                <p>
                    <strong>LinkedIn:</strong><br>
                    <a href="<?= escape($linkedin); ?>" target="_blank">
                        <?= escape($linkedin); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>

        <div class="contact-form-box">
            <?php if ($success): ?>
                <div class="public-alert-success">
                    <?= escape($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="public-alert-error">
                    <?= escape($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="contact-form">
                <label for="name">Nome</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= escape($_POST['name'] ?? ''); ?>"
                    required
                >

                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= escape($_POST['email'] ?? ''); ?>"
                    required
                >

                <label for="phone">Telefone / WhatsApp</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?= escape($_POST['phone'] ?? ''); ?>"
                >

                <label for="subject">Assunto</label>
                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="<?= escape($_POST['subject'] ?? ''); ?>"
                >

                <label for="message">Mensagem</label>
                <textarea
                    id="message"
                    name="message"
                    required
                ><?= escape($_POST['message'] ?? ''); ?></textarea>

                <button type="submit" class="btn-primary">
                    Enviar mensagem
                </button>
            </form>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>