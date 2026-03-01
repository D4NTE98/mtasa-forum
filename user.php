<?php
require __DIR__ . '/config.php';

$userId = (int)($_GET['id'] ?? 0);
if ($userId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT u.*, g.name AS group_name, g.color AS group_color, g.icon AS group_icon
                       FROM forum_users u
                       LEFT JOIN forum_groups g ON g.id = u.group_id
                       WHERE u.id = ?");
$stmt->execute([$userId]);
$u = $stmt->fetch();
if (!$u) {
    header('Location: index.php');
    exit;
}

$cntTopics = $pdo->prepare("SELECT COUNT(*) AS c FROM forum_topics WHERE user_id = ?");
$cntTopics->execute([$userId]);
$topicsCount = (int)($cntTopics->fetch()['c'] ?? 0);

$cntPosts = $pdo->prepare("SELECT COUNT(*) AS c FROM forum_posts WHERE user_id = ?");
$cntPosts->execute([$userId]);
$postsCount = (int)($cntPosts->fetch()['c'] ?? 0);

$title = $u['username'];
require __DIR__ . '/includes/header.php';
?>

<div class="panel">
  <div class="profile">
    <div class="profile-name"><?= render_username($u) ?></div>
    <div class="profile-sub"><?= e($u['group_name'] ?? 'Użytkownik') ?></div>

    <div class="profile-grid">
      <div class="profile-card">
        <div class="pc-title">Dołączył</div>
        <div class="pc-val"><?= e(date('d.m.Y', strtotime($u['created_at']))) ?></div>
      </div>
      <div class="profile-card">
        <div class="pc-title">Tematy</div>
        <div class="pc-val"><?= (int)$topicsCount ?></div>
      </div>
      <div class="profile-card">
        <div class="pc-title">Posty</div>
        <div class="pc-val"><?= (int)$postsCount ?></div>
      </div>
      <div class="profile-card">
        <div class="pc-title">Ostatnio online</div>
        <div class="pc-val"><?= e(date('d.m.Y H:i', strtotime($u['last_seen']))) ?></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
