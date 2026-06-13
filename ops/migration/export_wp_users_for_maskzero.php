<?php
declare(strict_types=1);

/**
 * One-time dadudekc WordPress user export for MaskZero migration.
 * Upload to dadudekc.site public_html, call with ?token=..., then delete.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$token = (string)($_GET['token'] ?? '');
$expected = getenv('SPARK_MIGRATE_EXPORT_TOKEN') ?: '';
if ($expected === '' && is_file(__DIR__ . '/.spark-migrate-export.token')) {
    $expected = trim((string)file_get_contents(__DIR__ . '/.spark-migrate-export.token'));
}

if ($token === '' || $expected === '' || !hash_equals($expected, $token)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Export token rejected.'], JSON_UNESCAPED_SLASHES);
    exit;
}

$wpLoad = __DIR__ . '/wp-load.php';
if (!is_file($wpLoad)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'wp-load.php not found on this host.'], JSON_UNESCAPED_SLASHES);
    exit;
}

require_once $wpLoad;

if (!function_exists('get_users')) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'WordPress user API unavailable.'], JSON_UNESCAPED_SLASHES);
    exit;
}

$rows = get_users([
    'fields' => ['ID', 'user_email', 'user_login', 'display_name', 'user_pass', 'user_registered'],
    'number' => 5000,
]);

$users = [];
foreach ($rows as $user) {
    $email = strtolower(trim((string)$user->user_email));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        continue;
    }
    $users[] = [
        'wp_user_id' => (int)$user->ID,
        'email' => $email,
        'user_login' => (string)$user->user_login,
        'display_name' => (string)($user->display_name ?: $user->user_login),
        'wp_pass_hash' => (string)$user->user_pass,
        'registered' => (string)$user->user_registered,
    ];
}

echo json_encode([
    'ok' => true,
    'source' => 'dadudekc.wordpress',
    'count' => count($users),
    'users' => $users,
], JSON_UNESCAPED_SLASHES);
