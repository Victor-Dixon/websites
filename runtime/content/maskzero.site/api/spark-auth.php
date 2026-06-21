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

function spark_auth_find_user(array $users, string $identifier): ?array {
    $needle = spark_auth_normalize_email($identifier);
    foreach ($users as $user) {
        $email = spark_auth_normalize_email((string)($user['email'] ?? ''));
        $display = strtolower((string)($user['display_name'] ?? ''));
        if ($email === $needle || $display === strtolower(trim($identifier))) {
            return $user;
        }
    }
    return null;
}

function spark_auth_public_user(array $user): array {
    return [
        'id' => (string)($user['id'] ?? ''),
        'email' => (string)($user['email'] ?? ''),
        'display_name' => (string)($user['display_name'] ?? ''),
    ];
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
    $user = spark_auth_find_user($users, $identifier);
    if (!$user || !password_verify($password, (string)($user['password_hash'] ?? ''))) {
        spark_auth_json(['ok' => false, 'message' => 'Login did not complete. Check your username and password, then try again.'], 401);
    }

    spark_auth_create_session($user, $redirect);
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

    spark_auth_json([
        'ok' => true,
        'logged_in' => true,
        'user' => spark_auth_public_user($current['user']),
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
    spark_auth_json(['ok' => false, 'message' => 'Unknown Spark auth action.'], 404);
} catch (Throwable $error) {
    error_log('MaskZero Spark auth error: ' . $error->getMessage());
    spark_auth_json(['ok' => false, 'message' => 'Spark auth server error.'], 500);
}
