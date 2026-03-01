<?php
declare(strict_types=1);

function e(?string $v): string {
    return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash_set(string $type, string $msg): void {
    $_SESSION['flash'][] = ['type' => $type, 'msg' => $msg];
}

function flash_get(): array {
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_verify(?string $token): bool {
    return isset($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

function auth_user(PDO $pdo): ?array {
    if (empty($_SESSION['uid'])) return null;
    $stmt = $pdo->prepare("SELECT u.*, g.name AS group_name, g.color AS group_color, g.icon AS group_icon, g.permission_level
                           FROM forum_users u
                           LEFT JOIN forum_groups g ON g.id = u.group_id
                           WHERE u.id = ?");
    $stmt->execute([$_SESSION['uid']]);
    $u = $stmt->fetch();
    return $u ?: null;
}

function require_login(PDO $pdo): array {
    $u = auth_user($pdo);
    if (!$u) {
        flash_set('error', 'Musisz się zalogować.');
        header('Location: login.php');
        exit;
    }
    return $u;
}

function render_username(array $user, bool $withIcon = true): string {
    $name = e($user['username'] ?? '');
    $color = $user['group_color'] ?? '#222';
    $icon = $user['group_icon'] ?? '';
    $iconHtml = ($withIcon && $icon) ? '<i class="fa-solid ' . e($icon) . '"></i>' : '';
    return '<span class="u-name" style="--u-color:' . e($color) . '">' . $iconHtml . '<span>' . $name . '</span></span>';
}

function render_content(string $content): string {
    $safe = e($content);
    $safe = preg_replace('/\r\n|\r|\n/', "\n", $safe);
    return nl2br($safe, false);
}

function online_users_count(PDO $pdo): int {
    $stmt = $pdo->query("SELECT COUNT(*) AS c FROM forum_users WHERE last_seen >= (NOW() - INTERVAL 5 MINUTE)");
    $row = $stmt->fetch();
    return (int)($row['c'] ?? 0);
}

function paginate(int $total, int $perPage, int $page): array {
    $pages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($pages, $page));
    $offset = ($page - 1) * $perPage;
    return [$page, $pages, $offset];
}

function nav_page_link(string $base, array $params, int $page): string {
    $params['page'] = $page;
    return $base . '?' . http_build_query($params);
}
