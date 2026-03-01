<?php
require __DIR__ . '/config.php';
$authUser = require_login($pdo);

$catId = (int)($_GET['cat'] ?? 0);
if ($catId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, name FROM forum_categories WHERE id = ?");
$stmt->execute([$catId]);
$cat = $stmt->fetch();
if (!$cat) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_verify($token)) {
        flash_set('error', 'Nieprawidłowy token formularza.');
        header('Location: new_topic.php?cat=' . $catId);
        exit;
    }

    $titleIn = trim((string)($_POST['title'] ?? ''));
    $contentIn = trim((string)($_POST['content'] ?? ''));

    if (mb_strlen($titleIn) < 3 || mb_strlen($titleIn) > 120) {
        flash_set('error', 'Tytuł musi mieć od 3 do 120 znaków.');
    } elseif (mb_strlen($contentIn) < 3) {
        flash_set('error', 'Treść jest za krótka.');
    } else {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO forum_topics (category_id, user_id, title, created_at, updated_at, views, replies_count, is_pinned, is_locked)
                               VALUES (?, ?, ?, NOW(), NOW(), 0, 0, 0, 0)");
        $stmt->execute([$catId, $authUser['id'], $titleIn]);
        $topicId = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO forum_posts (topic_id, user_id, content, created_at, updated_at)
                               VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$topicId, $authUser['id'], $contentIn]);

        $pdo->commit();

        flash_set('success', 'Temat utworzony.');
        header('Location: topic.php?id=' . $topicId);
        exit;
    }
}

$title = 'Nowy temat';
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumbs">
  <a href="index.php">Forum</a>
  <span>›</span>
  <a href="category.php?id=<?= (int)$catId ?>"><?= e($cat['name']) ?></a>
  <span>›</span>
  <span>Nowy temat</span>
</div>

<div class="panel form">
  <h1 class="page-title">Nowy temat</h1>

  <form method="post" class="form-grid">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label class="field">
      <span class="field-label">Tytuł</span>
      <input class="input" name="title" maxlength="120" value="<?= e($_POST['title'] ?? '') ?>" required>
    </label>

    <label class="field">
      <span class="field-label">Treść</span>
      <textarea class="textarea" name="content" rows="10" required><?= e($_POST['content'] ?? '') ?></textarea>
    </label>

    <div class="form-actions">
      <a class="btn btn-ghost" href="category.php?id=<?= (int)$catId ?>">Anuluj</a>
      <button class="btn" type="submit"><i class="fa-solid fa-paper-plane"></i> Opublikuj</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
