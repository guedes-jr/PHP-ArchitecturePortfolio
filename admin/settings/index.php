<?php

$pageTitle = 'Configurações do Site';

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$languages = [
    'pt' => 'Português',
    'en' => 'Inglês',
    'es' => 'Espanhol',
];

$settingKeys = [
    'site_name' => 'Nome do site',
    'hero_eyebrow' => 'Texto pequeno da Home',
    'hero_title' => 'Título principal da Home',
    'hero_description' => 'Descrição da Home',
    'about_title' => 'Título do Sobre',
    'about_text' => 'Texto do Sobre',
    'email' => 'E-mail',
    'whatsapp' => 'WhatsApp',
    'instagram' => 'Instagram',
    'linkedin' => 'LinkedIn',
];

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($languages as $languageCode => $languageName) {
        foreach ($settingKeys as $key => $label) {
            $value = trim($_POST[$languageCode][$key] ?? '');

            $stmt = $pdo->prepare("
                SELECT id
                FROM site_settings
                WHERE setting_key = :setting_key
                  AND language = :language
                LIMIT 1
            ");

            $stmt->execute([
                'setting_key' => $key,
                'language' => $languageCode,
            ]);

            $existing = $stmt->fetch();

            if ($existing) {
                $updateStmt = $pdo->prepare("
                    UPDATE site_settings
                    SET setting_value = :setting_value
                    WHERE id = :id
                ");

                $updateStmt->execute([
                    'setting_value' => $value,
                    'id' => $existing['id'],
                ]);
            } else {
                $insertStmt = $pdo->prepare("
                    INSERT INTO site_settings (
                        setting_key,
                        language,
                        setting_value
                    ) VALUES (
                        :setting_key,
                        :language,
                        :setting_value
                    )
                ");

                $insertStmt->execute([
                    'setting_key' => $key,
                    'language' => $languageCode,
                    'setting_value' => $value,
                ]);
            }
        }
    }

    $success = 'Configurações atualizadas com sucesso.';
}

$settings = [];

$stmt = $pdo->query("
    SELECT setting_key, language, setting_value
    FROM site_settings
");

foreach ($stmt->fetchAll() as $setting) {
    $settings[$setting['language']][$setting['setting_key']] = $setting['setting_value'];
}

?>

<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

<section class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1>Configurações</h1>
            <p>Edite os textos principais, contatos e informações institucionais do site.</p>
        </div>
    </div>

    <div class="admin-panel">
        <?php if ($success): ?>
            <div class="alert-success">
                <?= escape($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <?php foreach ($languages as $languageCode => $languageName): ?>
                <h2><?= escape($languageName); ?></h2>

                <?php foreach ($settingKeys as $key => $label): ?>
                    <label for="<?= escape($languageCode . '_' . $key); ?>">
                        <?= escape($label); ?>
                    </label>

                    <?php if (in_array($key, ['hero_description', 'about_text'], true)): ?>
                        <textarea
                            id="<?= escape($languageCode . '_' . $key); ?>"
                            name="<?= escape($languageCode); ?>[<?= escape($key); ?>]"
                        ><?= escape($settings[$languageCode][$key] ?? ''); ?></textarea>
                    <?php else: ?>
                        <input
                            type="text"
                            id="<?= escape($languageCode . '_' . $key); ?>"
                            name="<?= escape($languageCode); ?>[<?= escape($key); ?>]"
                            value="<?= escape($settings[$languageCode][$key] ?? ''); ?>"
                        >
                    <?php endif; ?>
                <?php endforeach; ?>

                <hr>
            <?php endforeach; ?>

            <div class="form-actions">
                <button type="submit" class="admin-button">
                    Salvar configurações
                </button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>