<?php
require __DIR__ . '/config.php';
$title = 'Forum';
$sections = $pdo->query("SELECT id, name, sort_order FROM forum_sections ORDER BY sort_order ASC, id ASC")->fetchAll();

$catsStmt = $pdo->query("SELECT c.id, c.section_id, c.name, c.description, c.icon
                         FROM forum_categories c
                         ORDER BY c.section_id ASC, c.sort_order ASC, c.id ASC");
$cats = [];
foreach ($catsStmt as $c) {
    $cats[(int)$c['section_id']][] = $c;
}

require __DIR__ . '/includes/header.php';
?>

<?php foreach ($sections as $s): ?>
  <section class="section">
    <h2 class="section-title"><?= e($s['name']) ?></h2>
    <div class="tiles">
      <?php foreach (($cats[(int)$s['id']] ?? []) as $c): ?>
        <a class="tile" href="category.php?id=<?= (int)$c['id'] ?>">
          <div class="tile-icon"><i class="fa-solid <?= e($c['icon']) ?>"></i></div>
          <div class="tile-title"><?= e($c['name']) ?></div>
          <div class="tile-sub"><?= e($c['description']) ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
<?php endforeach; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
