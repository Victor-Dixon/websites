<?php
declare(strict_types=1);

/**
 * MaskZero first-party auth endpoint.
 *
 * This keeps the public login/signup experience on MaskZero instead of relying
 * on a WordPress login screen. User data is stored outside the public route
 * tree in `.spark-auth`, which is blocked by the root .htaccess.
 */

const SPARK_AUTH_COOKIE = 'maskzero_spark_session';
const SPARK_AUTH_TTL = 1209600; // 14 days
const SPARK_ROOT_OWNER_EMAIL = 'dadudekc@gmail.com';

$_spark_entitlements = __DIR__ . '/spark-entitlements.php';
if (is_file($_spark_entitlements)) {
    require_once $_spark_entitlements;
}

function spark_auth_json(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function spark_auth_storage_dir(): string {
    $dir = dirname(__DIR__) . '/.spark-auth';
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
    return $dir;
}

function spark_auth_file(string $name): string {
    return spark_auth_storage_dir() . '/' . $name;
}

function spark_auth_read(string $name): array {
    $file = spark_auth_file($name);
    if (!is_file($file)) {
        return [];
    }

    $raw = file_get_contents($file);
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function spark_auth_write(string $name, array $data): void {
    $file = spark_auth_file($name);
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    chmod($file, 0600);
}

function spark_auth_input(): array {
    $data = $_POST;
    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (is_array($json)) {
            $data = array_merge($data, $json);
        }
    }
    return $data;
}

function spark_auth_normalize_email(string $email): string {
    return strtolower(trim($email));
}

function spark_auth_safe_display(string $display): string {
    $display = trim(strip_tags($display));
    $display = preg_replace('/\s+/', ' ', $display) ?: '';
    return substr($display, 0, 48);
}

function spark_auth_cookie_options(int $expires): array {
    return [
        'expires' => $expires,
        'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function spark_auth_find_user_index(array $users, string $identifier): ?int {
    $needle = spark_auth_normalize_email($identifier);
    $login = strtolower(trim($identifier));
    foreach ($users as $index => $user) {
        $email = spark_auth_normalize_email((string)($user['email'] ?? ''));
        $display = strtolower((string)($user['display_name'] ?? ''));
        $userLogin = strtolower((string)($user['user_login'] ?? ''));
        $username = strtolower((string)($user['username'] ?? ''));
        if (
            $email === $needle
            || $display === $login
            || ($userLogin !== '' && $userLogin === $login)
            || ($username !== '' && $username === $login)
        ) {
            return $index;
        }
    }
    return null;
}

function spark_auth_find_user(array $users, string $identifier): ?array {
    $index = spark_auth_find_user_index($users, $identifier);
    return $index === null ? null : $users[$index];
}

function spark_auth_migrate_token_ok(string $token): bool {
    if ($token === '') {
        return false;
    }
    $file = spark_auth_file('migrate.token');
    if (is_file($file)) {
        return hash_equals(trim((string)file_get_contents($file)), $token);
    }
    $env = getenv('SPARK_AUTH_MIGRATE_TOKEN');
    return is_string($env) && $env !== '' && hash_equals($env, $token);
}

function spark_auth_verify_wp_password(string $password, string $stored_hash): bool {
    if ($stored_hash === '' || $password === '') {
        return false;
    }
    if (strlen($password) > 4096) {
        return false;
    }

    // WordPress 6.8+ prefixed bcrypt: $wp$2y$...
    if (str_starts_with($stored_hash, '$wp')) {
        $password_to_verify = base64_encode(hash_hmac('sha384', $password, 'wp-sha384', true));
        return password_verify($password_to_verify, substr($stored_hash, 3));
    }

    // Legacy WordPress phpass portable hashes: $P$ / $H$
    if (str_starts_with($stored_hash, '$P$') || str_starts_with($stored_hash, '$H$')) {
        if (!defined('SPARK_AUTH_LOADING_PHPASS')) {
            define('SPARK_AUTH_LOADING_PHPASS', true);
        }
        require_once __DIR__ . '/class-phpass.php';
        $hasher = new PasswordHash(8, true);
        return $hasher->CheckPassword($password, $stored_hash);
    }

    // Very old md5-style hashes (32 chars or less).
    if (strlen($stored_hash) <= 32) {
        return hash_equals($stored_hash, md5($password));
    }

    // Plain bcrypt / argon2 and other password_hash() formats.
    return password_verify($password, $stored_hash);
}

function spark_auth_check_password(string $password, array &$user): bool {
    $hash = (string)($user['password_hash'] ?? '');
    if ($hash !== '' && password_verify($password, $hash)) {
        return true;
    }

    $wpHash = (string)($user['wp_pass_hash'] ?? '');
    if ($wpHash === '' || !spark_auth_verify_wp_password($password, $wpHash)) {
        return false;
    }

    $user['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    unset($user['wp_pass_hash']);
    $user['migrated_at'] = gmdate('c');
    return true;
}

function spark_auth_user_role(array $user): string {
    $stored = strtolower((string)($user['game_role'] ?? ''));
    if (in_array($stored, ['owner', 'admin', 'dev', 'moderator'], true)) {
        return $stored;
    }
    $email = spark_auth_normalize_email((string)($user['email'] ?? ''));
    if ($email === spark_auth_normalize_email(SPARK_ROOT_OWNER_EMAIL)) {
        return 'owner';
    }
    return 'player';
}

function spark_auth_user_is_owner(array $user): bool {
    return spark_auth_user_role($user) === 'owner';
}

function spark_auth_user_can_access_admin_panel(array $user): bool {
    return spark_auth_user_role($user) !== 'player';
}

function spark_auth_role_capabilities(string $role): array {
    $map = [
        'owner' => ['lookup_accounts', 'view_account_detail', 'grant_roles', 'revoke_roles', 'view_admin_log', 'view_debug_status', 'manage_ops'],
        'admin' => ['lookup_accounts', 'view_account_detail', 'view_admin_log', 'manage_ops'],
        'dev' => ['lookup_accounts', 'view_account_detail', 'view_debug_status', 'manage_ops'],
        'moderator' => ['lookup_accounts', 'view_account_detail'],
        'player' => [],
    ];
    return $map[$role] ?? $map['player'];
}

function spark_auth_require_admin(): array {
    $current = spark_auth_current_session();
    if (!$current) {
        spark_auth_json(['ok' => false, 'message' => 'Sign in required.'], 401);
    }
    if (!spark_auth_user_can_access_admin_panel($current['user'])) {
        spark_auth_json(['ok' => false, 'message' => 'Elevated MaskZero role required.'], 403);
    }
    return $current;
}

function spark_auth_audit(string $action, string $targetId, array $meta = []): void {
    $current = spark_auth_current_session();
    $actorId = $current ? (string)($current['user']['id'] ?? '') : 'system';
    $log = spark_auth_read('admin_audit.json');
    if (!is_array($log)) {
        $log = [];
    }
    $log[] = [
        'created_at' => gmdate('c'),
        'action' => $action,
        'actor_user_id' => $actorId,
        'target_user_id' => $targetId,
        'metadata' => $meta,
    ];
    if (count($log) > 500) {
        $log = array_slice($log, -500);
    }
    spark_auth_write('admin_audit.json', $log);
}

function spark_auth_account_summary(array $user): array {
    return [
        'user_id' => (string)($user['id'] ?? ''),
        'email' => (string)($user['email'] ?? ''),
        'display_name' => (string)($user['display_name'] ?? ''),
        'game_role' => spark_auth_user_role($user),
        'is_root_owner' => spark_auth_normalize_email((string)($user['email'] ?? '')) === spark_auth_normalize_email(SPARK_ROOT_OWNER_EMAIL),
        'created_at' => (string)($user['created_at'] ?? ''),
        'migrated_from' => (string)($user['migrated_from'] ?? ''),
    ];
}

function spark_auth_owner_session(): void {
    $current = spark_auth_require_admin();
    $user = $current['user'];
    $role = spark_auth_user_role($user);
    spark_auth_json([
        'ok' => true,
        'logged_in' => true,
        'user_id' => (string)($user['id'] ?? ''),
        'email' => (string)($user['email'] ?? ''),
        'display_name' => (string)($user['display_name'] ?? ''),
        'game_role' => $role,
        'is_owner' => spark_auth_user_is_owner($user),
        'is_root_owner' => spark_auth_normalize_email((string)($user['email'] ?? '')) === spark_auth_normalize_email(SPARK_ROOT_OWNER_EMAIL),
        'can_access_admin_panel' => true,
        'capabilities' => spark_auth_role_capabilities($role),
    ]);
}

function spark_auth_owner_search(string $query): void {
    spark_auth_require_admin();
    $query = trim($query);
    if ($query === '') {
        spark_auth_json(['ok' => true, 'results' => []]);
    }

    $users = spark_auth_read('users.json');
    $needle = strtolower($query);
    $results = [];
    foreach ($users as $user) {
        if (!is_array($user)) {
            continue;
        }
        $hay = strtolower(implode(' ', [
            (string)($user['id'] ?? ''),
            (string)($user['email'] ?? ''),
            (string)($user['display_name'] ?? ''),
            (string)($user['user_login'] ?? ''),
        ]));
        if (strpos($hay, $needle) === false) {
            continue;
        }
        $results[] = spark_auth_account_summary($user);
        if (count($results) >= 25) {
            break;
        }
    }

    spark_auth_json(['ok' => true, 'query' => $query, 'results' => $results]);
}

function spark_auth_owner_user(string $targetId): void {
    spark_auth_require_admin();
    $targetId = trim($targetId);
    if ($targetId === '') {
        spark_auth_json(['ok' => false, 'message' => 'User ID required.'], 422);
    }

    $users = spark_auth_read('users.json');
    foreach ($users as $user) {
        if (!is_array($user)) {
            continue;
        }
        if ((string)($user['id'] ?? '') === $targetId) {
            spark_auth_json(['ok' => true, 'account' => spark_auth_account_summary($user)]);
        }
    }
    spark_auth_json(['ok' => false, 'message' => 'Account not found.'], 404);
}

function spark_auth_owner_set_role(array $input, bool $revoke): void {
    $current = spark_auth_require_admin();
    $actor = $current['user'];
    if (!spark_auth_user_is_owner($actor)) {
        spark_auth_json(['ok' => false, 'message' => 'Only owners can change roles.'], 403);
    }

    $targetId = trim((string)($input['user_id'] ?? $input['target_id'] ?? ''));
    $newRole = $revoke ? 'player' : strtolower(trim((string)($input['role'] ?? 'player')));
    $valid = ['owner', 'admin', 'dev', 'moderator', 'player'];
    if (!in_array($newRole, $valid, true)) {
        spark_auth_json(['ok' => false, 'message' => 'Invalid role.'], 422);
    }

    $users = spark_auth_read('users.json');
    $found = false;
    foreach ($users as $index => $user) {
        if (!is_array($user) || (string)($user['id'] ?? '') !== $targetId) {
            continue;
        }
        $found = true;
        $email = spark_auth_normalize_email((string)($user['email'] ?? ''));
        if ($email === spark_auth_normalize_email(SPARK_ROOT_OWNER_EMAIL) && $newRole !== 'owner') {
            spark_auth_json(['ok' => false, 'message' => 'Root owner role cannot be removed.'], 403);
        }
        $previous = spark_auth_user_role($user);
        if ($newRole === 'player') {
            unset($users[$index]['game_role']);
        } else {
            $users[$index]['game_role'] = $newRole;
        }
        spark_auth_write('users.json', array_values($users));
        spark_auth_audit($revoke ? 'revoke_role' : 'grant_role', $targetId, [
            'previous_role' => $previous,
            'new_role' => $newRole,
        ]);
        spark_auth_json([
            'ok' => true,
            'user_id' => $targetId,
            'game_role' => $newRole,
            'account' => spark_auth_account_summary($users[$index]),
        ]);
    }

    if (!$found) {
        spark_auth_json(['ok' => false, 'message' => 'Account not found.'], 404);
    }
}

function spark_auth_owner_audit(?string $targetId): void {
    spark_auth_require_admin();
    $actor = spark_auth_current_session()['user'];
    $caps = spark_auth_role_capabilities(spark_auth_user_role($actor));
    if (!in_array('view_admin_log', $caps, true)) {
        spark_auth_json(['ok' => false, 'message' => 'Audit log access denied.'], 403);
    }

    $log = spark_auth_read('admin_audit.json');
    if (!is_array($log)) {
        $log = [];
    }
    $entries = array_reverse($log);
    if ($targetId) {
        $entries = array_values(array_filter($entries, static function ($row) use ($targetId) {
            return is_array($row) && (string)($row['target_user_id'] ?? '') === $targetId;
        }));
    }
    spark_auth_json(['ok' => true, 'entries' => array_slice($entries, 0, 100)]);
}

function spark_auth_owner_debug(): void {
    $current = spark_auth_require_admin();
    $caps = spark_auth_role_capabilities(spark_auth_user_role($current['user']));
    if (!in_array('view_debug_status', $caps, true)) {
        spark_auth_json(['ok' => false, 'message' => 'Debug access denied.'], 403);
    }

    $users = spark_auth_read('users.json');
    $sessions = spark_auth_read('sessions.json');
    spark_auth_json([
        'ok' => true,
        'user_count' => is_array($users) ? count($users) : 0,
        'active_sessions' => is_array($sessions) ? count($sessions) : 0,
        'storage_dir' => 'protected',
        'root_owner_email' => SPARK_ROOT_OWNER_EMAIL,
        'php_version' => PHP_VERSION,
    ]);
}

function spark_auth_public_user(array $user): array {
    $role = spark_auth_user_role($user);
    $payload = [
        'id' => (string)($user['id'] ?? ''),
        'email' => (string)($user['email'] ?? ''),
        'display_name' => (string)($user['display_name'] ?? ''),
        'game_role' => $role,
        'is_owner' => spark_auth_user_is_owner($user),
        'can_access_admin_panel' => spark_auth_user_can_access_admin_panel($user),
    ];
    if (function_exists('spark_ent_render_access')) {
        $payload = array_merge($payload, spark_ent_render_access($user));
    }
    return $payload;
}

function spark_auth_current_session(): ?array {
    $token = $_COOKIE[SPARK_AUTH_COOKIE] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }

    $sessions = spark_auth_read('sessions.json');
    $session = $sessions[$token] ?? null;
    if (!is_array($session) || (int)($session['expires_at'] ?? 0) < time()) {
        unset($sessions[$token]);
        spark_auth_write('sessions.json', $sessions);
        return null;
    }

    $users = spark_auth_read('users.json');
    foreach ($users as $user) {
        if (($user['id'] ?? '') === ($session['user_id'] ?? '')) {
            return ['token' => $token, 'session' => $session, 'user' => $user];
        }
    }
    return null;
}

function spark_auth_register(array $input): void {
    $email = spark_auth_normalize_email((string)($input['email'] ?? ''));
    $display = spark_auth_safe_display((string)($input['username'] ?? $input['display_name'] ?? ''));
    $password = (string)($input['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        spark_auth_json(['ok' => false, 'message' => 'Enter a valid email address.'], 422);
    }
    if (strlen($password) < 8) {
        spark_auth_json(['ok' => false, 'message' => 'Password must be at least 8 characters.'], 422);
    }
    if ($display === '') {
        $display = explode('@', $email)[0];
    }

    $users = spark_auth_read('users.json');
    foreach ($users as $user) {
        if (spark_auth_normalize_email((string)($user['email'] ?? '')) === $email) {
            spark_auth_json(['ok' => false, 'message' => 'That email already has a Spark account. Try logging in.'], 409);
        }
    }

    $user = [
        'id' => 'spark_' . hash('sha256', $email . '|' . microtime(true)),
        'email' => $email,
        'display_name' => $display,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'spark_plan' => 'free',
        'skymotion_render_credits' => 0,
        'created_at' => gmdate('c'),
    ];
    $users[] = $user;
    spark_auth_write('users.json', $users);

    spark_auth_create_session($user, '/spark-generator/');
}

function spark_auth_create_session(array $user, string $redirect): void {
    $token = bin2hex(random_bytes(32));
    $expires = time() + SPARK_AUTH_TTL;
    $sessions = spark_auth_read('sessions.json');
    $sessions[$token] = [
        'user_id' => (string)$user['id'],
        'created_at' => gmdate('c'),
        'expires_at' => $expires,
    ];
    spark_auth_write('sessions.json', $sessions);
    setcookie(SPARK_AUTH_COOKIE, $token, spark_auth_cookie_options($expires));
    spark_auth_json([
        'ok' => true,
        'logged_in' => true,
        'redirect' => $redirect,
        'user' => spark_auth_public_user($user),
    ]);
}

function spark_auth_login(array $input): void {
    $identifier = trim((string)($input['log'] ?? $input['email'] ?? $input['username'] ?? ''));
    $password = (string)($input['pwd'] ?? $input['password'] ?? '');
    $redirect = (string)($input['redirect_to'] ?? '/spark-dashboard/');
    if ($redirect === '' || strpos($redirect, '//') === 0 || strpos($redirect, '/') !== 0) {
        $redirect = '/spark-dashboard/';
    }

    if ($identifier === '' || $password === '') {
        spark_auth_json(['ok' => false, 'message' => 'Enter your email/display name and password.'], 422);
    }

    $users = spark_auth_read('users.json');
    if (!is_array($users)) {
        $users = [];
    }
    $userIndex = spark_auth_find_user_index($users, $identifier);
    if ($userIndex === null) {
        spark_auth_json(['ok' => false, 'message' => 'Login did not complete. Check your username and password, then try again.'], 401);
    }

    $user = $users[$userIndex];
    if (!spark_auth_check_password($password, $user)) {
        spark_auth_json(['ok' => false, 'message' => 'Login did not complete. Check your username and password, then try again.'], 401);
    }

    $users[$userIndex] = $user;
    spark_auth_write('users.json', $users);
    spark_auth_create_session($user, $redirect);
}

function spark_auth_import(array $input): void {
    $token = (string)($input['token'] ?? $_GET['token'] ?? '');
    if (!spark_auth_migrate_token_ok($token)) {
        spark_auth_json(['ok' => false, 'message' => 'Migration token rejected.'], 403);
    }

    $batch = $input['users'] ?? null;
    if (!is_array($batch)) {
        spark_auth_json(['ok' => false, 'message' => 'Expected users array.'], 422);
    }

    $users = spark_auth_read('users.json');
    if (!is_array($users)) {
        $users = [];
    }

    $imported = 0;
    $skipped = 0;
    foreach ($batch as $row) {
        if (!is_array($row)) {
            continue;
        }
        $email = spark_auth_normalize_email((string)($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        if (spark_auth_find_user_index($users, $email) !== null) {
            $skipped++;
            continue;
        }

        $display = spark_auth_safe_display((string)($row['display_name'] ?? $row['user_login'] ?? ''));
        if ($display === '') {
            $display = explode('@', $email)[0];
        }

        $users[] = [
            'id' => 'spark_wp_' . hash('sha256', $email . '|' . (string)($row['wp_user_id'] ?? '')),
            'email' => $email,
            'display_name' => $display,
            'user_login' => sanitize_text_field((string)($row['user_login'] ?? '')),
            'wp_pass_hash' => (string)($row['wp_pass_hash'] ?? $row['user_pass'] ?? ''),
            'password_hash' => '',
            'created_at' => (string)($row['registered'] ?? gmdate('c')),
            'migrated_from' => 'dadudekc.wordpress',
            'wp_user_id' => (int)($row['wp_user_id'] ?? 0),
        ];
        $imported++;
    }

    spark_auth_write('users.json', $users);
    spark_auth_json([
        'ok' => true,
        'imported' => $imported,
        'skipped' => $skipped,
        'total' => count($users),
    ]);
}

function sanitize_text_field(string $value): string {
    return trim(strip_tags($value));
}

function spark_auth_logout(): void {
    $token = $_COOKIE[SPARK_AUTH_COOKIE] ?? '';
    if (is_string($token) && $token !== '') {
        $sessions = spark_auth_read('sessions.json');
        unset($sessions[$token]);
        spark_auth_write('sessions.json', $sessions);
    }
    setcookie(SPARK_AUTH_COOKIE, '', spark_auth_cookie_options(time() - 3600));
    spark_auth_json(['ok' => true, 'logged_in' => false, 'redirect' => '/spark-login/']);
}

function spark_auth_session(): void {
    $current = spark_auth_current_session();
    if (!$current) {
        spark_auth_json(['ok' => true, 'logged_in' => false, 'user' => null]);
    }

    $public = spark_auth_public_user($current['user']);
    spark_auth_json([
        'ok' => true,
        'logged_in' => true,
        'user' => $public,
        'game_role' => $public['game_role'],
        'is_owner' => $public['is_owner'],
        'can_access_admin_panel' => $public['can_access_admin_panel'],
        'email' => $public['email'],
        'display_name' => $public['display_name'],
        'can_render_video' => (bool)($public['can_render_video'] ?? false),
        'skymotion_access' => (bool)($public['skymotion_access'] ?? false),
        'spark_plan' => (string)($public['spark_plan'] ?? 'free'),
        'skymotion_render_credits' => (int)($public['skymotion_render_credits'] ?? 0),
        'is_staff' => (bool)($public['is_staff'] ?? false),
    ]);
}

$action = (string)($_GET['action'] ?? $_POST['action'] ?? '');
$input = spark_auth_input();

try {
    if ($action === 'register') {
        spark_auth_register($input);
    }
    if ($action === 'login') {
        spark_auth_login($input);
    }
    if ($action === 'logout') {
        spark_auth_logout();
    }
    if ($action === 'session') {
        spark_auth_session();
    }
    if ($action === 'import') {
        spark_auth_import($input);
    }
    if ($action === 'owner_session') {
        spark_auth_owner_session();
    }
    if ($action === 'owner_search') {
        spark_auth_owner_search((string)($_GET['q'] ?? $input['q'] ?? ''));
    }
    if ($action === 'owner_user') {
        spark_auth_owner_user((string)($_GET['id'] ?? $input['id'] ?? ''));
    }
    if ($action === 'owner_grant_role') {
        spark_auth_owner_set_role($input, false);
    }
    if ($action === 'owner_revoke_role') {
        spark_auth_owner_set_role($input, true);
    }
    if ($action === 'owner_audit') {
        spark_auth_owner_audit(trim((string)($_GET['target_id'] ?? $input['target_id'] ?? '')) ?: null);
    }
    if ($action === 'owner_debug') {
        spark_auth_owner_debug();
    }
    spark_auth_json(['ok' => false, 'message' => 'Unknown Spark auth action.'], 404);
} catch (Throwable $error) {
    spark_auth_json(['ok' => false, 'message' => 'Spark auth server error.', 'debug' => $error->getMessage()], 500);
}
