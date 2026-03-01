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
        header('Location: register.php');
        exit;
    }

    $username = trim((string)($_POST['username'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $pass1 = (string)($_POST['password'] ?? '');
    $pass2 = (string)($_POST['password2'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        flash_set('error', 'Nick: 3-20 znaków, tylko litery/cyfry/_');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash_set('error', 'Podaj poprawny email.');
    } elseif (mb_strlen($pass1) < 6) {
        flash_set('error', 'Hasło musi mieć min. 6 znaków.');
    } elseif ($pass1 !== $pass2) {
        flash_set('error', 'Hasła nie są takie same.');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM forum_users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            flash_set('error', 'Użytkownik lub email już istnieje.');
        } else {
            $hash = password_hash($pass1, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO forum_users (username, email, password_hash, group_id, created_at, last_seen)
                                   VALUES (?, ?, ?, (SELECT id FROM forum_groups WHERE permission_level = 1 ORDER BY id ASC LIMIT 1), NOW(), NOW())");
            $stmt->execute([$username, $email, $hash]);
            $_SESSION['uid'] = (int)$pdo->lastInsertId();
            flash_set('success', 'Konto utworzone. Jesteś zalogowany.');
            header('Location: index.php');
            exit;
        }
    }
}

$title = 'Rejestracja';
require __DIR__ . '/includes/header.php';
?>

<div class="auth">
  <div class="panel form auth-card">
    <h1 class="page-title">Rejestracja</h1>

    <form method="post" class="form-grid">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

      <label class="field">
        <span class="field-label">Nick</span>
        <input class="input" name="username" value="<?= e($_POST['username'] ?? '') ?>" required>
      </label>

      <label class="field">
        <span class="field-label">Email</span>
        <input class="input" type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
      </label>

      <label class="field">
        <span class="field-label">Hasło</span>
        <input class="input" type="password" name="password" required>
      </label>

      <label class="field">
        <span class="field-label">Powtórz hasło</span>
        <input class="input" type="password" name="password2" required>
      </label>

      <div class="form-actions">
        <button class="btn" type="submit"><i class="fa-solid fa-user-plus"></i> Utwórz konto</button>
      </div>
    </form>

    <div class="auth-foot">
      Masz konto? <a href="login.php">Zaloguj się</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
