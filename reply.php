<?php
require __DIR__ . '/config.php';
$authUser = require_login($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$token = $_POST['csrf'] ?? '';
if (!csrf_verify($token)) {
    flash_set('error', 'Nieprawidłowy token formularza.');
    header('Location: index.php');
    exit;
}

$topicId = (int)($_POST['topic_id'] ?? 0);
$content = trim((string)($_POST['content'] ?? ''));

if ($topicId <= 0 || mb_strlen($content) < 1) {
    flash_set('error', 'Uzupełnij treść.');
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, is_locked FROM forum_topics WHERE id = ?");
$stmt->execute([$topicId]);
$topic = $stmt->fetch();
if (!$topic) {
    flash_set('error', 'Nie znaleziono tematu.');
    header('Location: index.php');
    exit;
}
if ((int)$topic['is_locked'] === 1) {
    flash_set('error', 'Temat jest zamknięty.');
    header('Location: topic.php?id=' . $topicId);
    exit;
}

$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO forum_posts (topic_id, user_id, content, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
$stmt->execute([$topicId, $authUser['id'], $content]);

$pdo->prepare("UPDATE forum_topics SET replies_count = replies_count + 1, updated_at = NOW() WHERE id = ?")->execute([$topicId]);

$pdo->commit();

flash_set('success', 'Odpowiedź dodana.');
header('Location: topic.php?id=' . $topicId . '#reply');
exit;
