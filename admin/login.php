<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

if (isLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Informe e-mail e senha.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([
            'email' => $email,
        ]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
            ];

            redirect(BASE_URL . '/admin/dashboard.php');
        }

        $error = 'E-mail ou senha inválidos.';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <link rel="stylesheet" href="<?= BASE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-login-body">

    <main class="login-container">
        <form method="POST" class="login-card">
            <h1>Painel Administrativo</h1>
            <p>Acesse para gerenciar os conteúdos do site.</p>

            <?php if ($error): ?>
                <div class="alert-error">
                    <?= escape($error); ?>
                </div>
            <?php endif; ?>

            <label for="email">E-mail</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="admin@site.com"
                required
            >

            <label for="password">Senha</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Sua senha"
                required
            >

            <button type="submit">Entrar</button>

            <a href="<?= BASE_URL; ?>/index.php" class="back-link">
                Voltar para o site
            </a>
        </form>
    </main>

</body>
</html>