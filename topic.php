<?php
require __DIR__ . '/config.php';

$topicId = (int)($_GET['id'] ?? 0);
if ($topicId <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, c.name AS category_name, c.id AS category_id, s.name AS section_name,
                              u.username, g.color AS group_color, g.icon AS group_icon
                       FROM forum_topics t
                       JOIN forum_categories c ON c.id = t.category_id
                       JOIN forum_sections s ON s.id = c.section_id
                       JOIN forum_users u ON u.id = t.user_id
                       LEFT JOIN forum_groups g ON g.id = u.group_id
                       WHERE t.id = ?");
$stmt->execute([$topicId]);
$topic = $stmt->fetch();
if (!$topic) {
    header('Location: index.php');
    exit;
}

$pdo->prepare("UPDATE forum_topics SET views = views + 1 WHERE id = ?")->execute([$topicId]);

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

$countStmt = $pdo->prepare("SELECT COUNT(*) AS c FROM forum_posts WHERE topic_id = ?");
$countStmt->execute([$topicId]);
$total = (int)($countStmt->fetch()['c'] ?? 0);

[$page, $pages, $offset] = paginate($total, $perPage, $page);

$postsStmt = $pdo->prepare("SELECT p.*, u.username, u.created_at AS user_created_at, g.name AS group_name, g.color AS group_color, g.icon AS group_icon
                            FROM forum_posts p
                            JOIN forum_users u ON u.id = p.user_id
                            LEFT JOIN forum_groups g ON g.id = u.group_id
                            WHERE p.topic_id = ?
                            ORDER BY p.created_at ASC
                            LIMIT {$perPage} OFFSET {$offset}");
$postsStmt->execute([$topicId]);
$posts = $postsStmt->fetchAll();

$title = $topic['title'];
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumbs">
  <a href="index.php">Forum</a>
  <span>›</span>
  <span><?= e($topic['section_name']) ?></span>
  <span>›</span>
  <a href="category.php?id=<?= (int)$topic['category_id'] ?>"><?= e($topic['category_name']) ?></a>
  <span>›</span>
  <span><?= e($topic['title']) ?></span>
</div>

<div class="bar">
  <div class="bar-left">
    <h1 class="page-title"><?= e($topic['title']) ?></h1>
    <div class="page-sub">
      Autor: <a href="user.php?id=<?= (int)$topic['user_id'] ?>"><?= render_username($topic) ?></a>
      <span class="dot">•</span>
      <?= e(date('d.m.Y H:i', strtotime($topic['created_at']))) ?>
      <span class="dot">•</span>
      <?= (int)$topic['views'] + 1 ?> wyświetleń
    </div>
  </div>
  <div class="bar-right">
    <?php if ($authUser): ?>
      <a class="btn" href="#reply"><i class="fa-solid fa-reply"></i> Odpowiedz</a>
    <?php else: ?>
      <a class="btn" href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Zaloguj, aby odpisać</a>
    <?php endif; ?>
  </div>
</div>

<div class="posts">
  <?php foreach ($posts as $p): ?>
    <article class="post">
      <aside class="post-side">
        <div class="post-user">
          <a class="post-user-name" href="user.php?id=<?= (int)$p['user_id'] ?>"><?= render_username($p) ?></a>
          <div class="post-user-meta"><?= e($p['group_name'] ?? 'Użytkownik') ?></div>
          <div class="post-user-meta">Dołączył: <?= e(date('d.m.Y', strtotime($p['user_created_at']))) ?></div>
        </div>
      </aside>
      <div class="post-body">
        <div class="post-top">
          <div class="post-date"><?= e(date('d.m.Y H:i', strtotime($p['created_at']))) ?></div>
        </div>
        <div class="post-content"><?= render_content((string)$p['content']) ?></div>
      </div>
    </article>
  <?php endforeach; ?>
</div>

<?php if ($pages > 1): ?>
  <div class="pager">
    <?php if ($page > 1): ?>
      <a class="pager-btn" href="<?= e(nav_page_link('topic.php', ['id' => $topicId], $page - 1)) ?>"><i class="fa-solid fa-chevron-left"></i></a>
    <?php endif; ?>
    <div class="pager-info"><?= (int)$page ?> / <?= (int)$pages ?></div>
    <?php if ($page < $pages): ?>
      <a class="pager-btn" href="<?= e(nav_page_link('topic.php', ['id' => $topicId], $page + 1)) ?>"><i class="fa-solid fa-chevron-right"></i></a>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if ($authUser && (int)$topic['is_locked'] === 0): ?>
  <div class="panel form" id="reply">
    <h2 class="section-title">Odpowiedź</h2>
    <form method="post" action="reply.php">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="topic_id" value="<?= (int)$topicId ?>">
      <label class="field">
        <textarea class="textarea" name="content" rows="8" required></textarea>
      </label>
      <div class="form-actions">
        <button class="btn" type="submit"><i class="fa-solid fa-paper-plane"></i> Wyślij</button>
      </div>
    </form>
  </div>
<?php elseif ((int)$topic['is_locked'] === 1): ?>
  <div class="panel">
    <div class="empty"><i class="fa-solid fa-lock"></i> Temat jest zamknięty.</div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
