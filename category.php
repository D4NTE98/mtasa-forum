<?php
require __DIR__ . '/config.php';

$catId = (int)($_GET['id'] ?? 0);
if ($catId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, s.name AS section_name
                       FROM forum_categories c
                       JOIN forum_sections s ON s.id = c.section_id
                       WHERE c.id = ?");
$stmt->execute([$catId]);
$cat = $stmt->fetch();
if (!$cat) {
    header('Location: index.php');
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$countStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM forum_topics WHERE category_id = ?");
$countStmt->execute([$catId]);
$total = (int)($countStmt->fetch()['c'] ?? 0);

[$page, $pages, $offset] = paginate($total, $perPage, $page);

$listStmt = $pdo->prepare("SELECT t.*, u.username, g.color AS group_color, g.icon AS group_icon
                           FROM forum_topics t
                           JOIN forum_users u ON u.id = t.user_id
                           LEFT JOIN forum_groups g ON g.id = u.group_id
                           WHERE t.category_id = ?
                           ORDER BY t.is_pinned DESC, t.updated_at DESC
                           LIMIT {$perPage} OFFSET {$offset}");
$listStmt->execute([$catId]);
$topics = $listStmt->fetchAll();

$title = $cat['name'];
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumbs">
  <a href="index.php">Forum</a>
  <span>›</span>
  <span><?= e($cat['section_name']) ?></span>
  <span>›</span>
  <span><?= e($cat['name']) ?></span>
</div>

<div class="bar">
  <div class="bar-left">
    <h1 class="page-title"><?= e($cat['name']) ?></h1>
    <div class="page-sub"><?= e($cat['description']) ?></div>
  </div>
  <div class="bar-right">
    <?php if ($authUser): ?>
      <a class="btn" href="new_topic.php?cat=<?= (int)$catId ?>"><i class="fa-solid fa-plus"></i> Nowy temat</a>
    <?php else: ?>
      <a class="btn" href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Zaloguj, aby pisać</a>
    <?php endif; ?>
  </div>
</div>

<div class="panel">
  <div class="topic-head">
    <div>Tytuł</div>
    <div class="th-center">Odpowiedzi</div>
    <div class="th-center">Wyświetlenia</div>
    <div class="th-right">Ostatnia aktywność</div>
  </div>

  <?php if (!$topics): ?>
    <div class="empty">Brak tematów w tym dziale.</div>
  <?php endif; ?>

  <?php foreach ($topics as $t): ?>
    <div class="topic-row">
      <div class="topic-main">
        <div class="topic-title">
          <?php if ((int)$t['is_pinned'] === 1): ?><span class="tag tag-pin"><i class="fa-solid fa-thumbtack"></i> Przypięte</span><?php endif; ?>
          <?php if ((int)$t['is_locked'] === 1): ?><span class="tag tag-lock"><i class="fa-solid fa-lock"></i> Zamknięte</span><?php endif; ?>
          <a href="topic.php?id=<?= (int)$t['id'] ?>"><?= e($t['title']) ?></a>
        </div>
        <div class="topic-meta">
          <span>Autor: <a href="user.php?id=<?= (int)$t['user_id'] ?>"><?= render_username($t) ?></a></span>
          <span>•</span>
          <span><?= e(date('d.m.Y H:i', strtotime($t['created_at']))) ?></span>
        </div>
      </div>
      <div class="th-center"><?= (int)$t['replies_count'] ?></div>
      <div class="th-center"><?= (int)$t['views'] ?></div>
      <div class="th-right"><?= e(date('d.m.Y H:i', strtotime($t['updated_at']))) ?></div>
    </div>
  <?php endforeach; ?>
</div>

<?php if ($pages > 1): ?>
  <div class="pager">
    <?php if ($page > 1): ?>
      <a class="pager-btn" href="<?= e(nav_page_link('category.php', ['id' => $catId], $page - 1)) ?>"><i class="fa-solid fa-chevron-left"></i></a>
    <?php endif; ?>
    <div class="pager-info"><?= (int)$page ?> / <?= (int)$pages ?></div>
    <?php if ($page < $pages): ?>
      <a class="pager-btn" href="<?= e(nav_page_link('category.php', ['id' => $catId], $page + 1)) ?>"><i class="fa-solid fa-chevron-right"></i></a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
