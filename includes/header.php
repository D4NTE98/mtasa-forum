<?php
if (!isset($pdo)) require __DIR__ . '/../config.php';
$authUser = $authUser ?? auth_user($pdo);
$onlineCount = online_users_count($pdo);
$flash = flash_get();
?><!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title ?? 'FORUM') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script>(()=>{try{const s=localStorage.getItem('theme');const t=s||((window.matchMedia&&window.matchMedia('(prefers-color-scheme: dark)').matches)?'dark':'light');document.documentElement.dataset.theme=t}catch(e){}})();</script>
<link href="assets/style.css" rel="stylesheet">
</head>
<body>
<header class="hero">
  <div class="hero-top">
    <a class="brand" href="index.php">
      <span class="brand-mark">FORUM</span>
      <span class="brand-sub">MTA</span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>

    <nav class="nav" id="topNav">
      <a href="index.php" class="nav-link">FORUM</a>
      <a href="about.php" class="nav-link">O NAS</a>
      <a href="contact.php" class="nav-link">KONTAKT</a>
      <?php if ($authUser): ?>
        <a href="user.php?id=<?= (int)$authUser['id'] ?>" class="nav-link"><?= render_username($authUser, false) ?></a>
        <a href="logout.php" class="nav-link nav-cta">WYLOGUJ</a>
      <?php else: ?>
        <a href="login.php" class="nav-link nav-cta">ZALOGUJ</a>
      <?php endif; ?>
    </nav>
  </div>

  <div class="hero-badge">
    <span class="pill"><i class="fa-solid fa-users"></i> <?= (int)$onlineCount ?> GRACZY ONLINE</span>
  </div>
</header>

<main class="wrap">
  <?php if ($flash): ?>
    <div class="flash">
      <?php foreach ($flash as $f): ?>
        <div class="flash-item flash-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
