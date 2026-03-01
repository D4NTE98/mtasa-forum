<?php
require __DIR__ . '/config.php';

if ($authUser) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_verify($token)) {
        flash_set('error', 'Nieprawidłowy token formularza.');
        header('Location: login.php');
        exit;
    }

    $login = trim((string)($_POST['login'] ?? ''));
    $pass = (string)($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT id, password_hash FROM forum_users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->execute([$login, $login]);
    $u = $stmt->fetch();

    if (!$u || !password_verify($pass, (string)$u['password_hash'])) {
        flash_set('error', 'Nieprawidłowe dane logowania.');
    } else {
        $_SESSION['uid'] = (int)$u['id'];
        flash_set('success', 'Zalogowano.');
        header('Location: index.php');
        exit;
    }
}

$title = 'Logowanie';
require __DIR__ . '/includes/header.php';
?>

<div class="auth">
  <div class="panel form auth-card">
    <h1 class="page-title">Logowanie</h1>

    <form method="post" class="form-grid">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <label class="field">
        <span class="field-label">Nick lub email</span>
        <input class="input" name="login" value="<?= e($_POST['login'] ?? '') ?>" required>
      </label>

      <label class="field">
        <span class="field-label">Hasło</span>
        <input class="input" type="password" name="password" required>
      </label>

      <div class="form-actions">
        <button class="btn" type="submit"><i class="fa-solid fa-right-to-bracket"></i> Zaloguj</button>
        <a class="btn btn-ghost" href="register.php">Rejestracja</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
