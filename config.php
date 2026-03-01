<?php
declare(strict_types=1);

session_start();

$DB_HOST = 'localhost';
$DB_NAME = 'forum';
$DB_USER = 'root';
$DB_PASS = 'pass';

$pdo = new PDO(
    "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
    $DB_USER,
    $DB_PASS,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

require __DIR__ . '/includes/functions.php';

$authUser = auth_user($pdo);

if ($authUser) {
    $stmt = $pdo->prepare("UPDATE forum_users SET last_seen = NOW() WHERE id = ?");
    $stmt->execute([$authUser['id']]);
}
