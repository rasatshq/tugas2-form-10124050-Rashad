<?php
require __DIR__ . '/app.php';
if (isset($_SESSION['user'])) { redirect('index.php'); }

$error = '';
$username = '';
$isDbError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post('username');
    $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
    if ($username === '' || $password === '') {
        $error = t('required');
    } elseif (mb_strlen($username) > 50 || strlen($password) > 72) {
        $error = t('login_limits');
    } else {
        try {
            $query = db()->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
            $query->execute([$username]);
            $user = $query->fetch();
            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username']];
                redirect('index.php');
            } else {
                $error = t('login_failed');
            }
        } catch (PDOException $exception) {
            error_log($exception->getMessage());
            $error = t('db_error');
            $isDbError = true;
        }
    }
}

header_page(t('login'), t('badge_native'));
?>

<div class="presentation-header">
    <h1 class="presentation-title">Stockify<span class="brand-dot">.</span> <?= $_SESSION['language'] === 'en' ? 'Item Inventory System' : 'Sistem Inventaris Barang' ?></h1>
    <p class="presentation-sub"><?= $_SESSION['language'] === 'en' ? 'Authentication Flow & Session Security Preview' : 'Pratinjau Alur Otentikasi & Konfirmasi Hapus Data (PHP Session Security)' ?></p>
</div>

<section class="login-card">
    <div class="login-header-row">
        <div>
            <div class="card-step-tag tag-login">HALAMAN 1</div>
            <h2><?= e(t('login_title')) ?></h2>
        </div>
        <div class="login-lang-box">
            <form class="lang-form" method="post">
                <?php csrf(); ?>
                <input type="hidden" name="action" value="language">
                <div class="lang-capsule">
                    <select id="language-card" name="language" onchange="if (this.form.requestSubmit) { this.form.requestSubmit(); } else { this.form.submit(); }">
                        <option value="id" <?= $_SESSION['language'] === 'id' ? 'selected' : '' ?>>Bahasa Indonesia</option>
                        <option value="en" <?= $_SESSION['language'] === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <p class="login-subtitle"><?= e(t('login_subtitle')) ?></p>

    <?php if ($error && !$isDbError): ?>
        <div class="alert alert-danger" role="alert">
            <div class="alert-content">
                <span class="alert-icon">⚠</span>
                <div>
                    <strong><?= e($error) ?></strong>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form method="post" class="login-form">
        <?php csrf(); ?>
        <div class="field-item">
            <div class="field-label-row">
                <label for="username"><?= e(t('username')) ?></label>
            </div>
            <input id="username" name="username" value="<?= e($username) ?>" maxlength="50" autocomplete="username" placeholder="admin" required>
        </div>

        <div class="field-item">
            <div class="field-label-row">
                <label for="password"><?= e(t('password')) ?></label>
            </div>
            <input id="password" name="password" type="password" maxlength="72" autocomplete="current-password" placeholder="••••••••••••" required>
        </div>

        <button type="submit" class="btn-primary btn-block"><?= e(t('login_btn')) ?></button>
    </form>
</section>

<?php if ($isDbError): ?>
    <div class="db-error-box">
        <div class="db-error-content">
            <span class="db-error-badge">!</span>
            <div>
                <strong><?= e(t('db_error_title')) ?></strong>
                <p><?= e(t('db_error_desc')) ?></p>
            </div>
        </div>
        <code class="db-error-code">PDOException</code>
    </div>
<?php endif; ?>

<?php footer_page(t('footer_list')); ?>
