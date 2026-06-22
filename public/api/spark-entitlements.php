<?php
declare(strict_types=1);

/**
 * Shared MaskZero entitlements for SkyMotion video renders.
 * Reads the same `.spark-auth` store as spark-auth.php.
 */

if (!defined('SPARK_AUTH_COOKIE')) {
    define('SPARK_AUTH_COOKIE', 'maskzero_spark_session');
}
if (!defined('SPARK_ROOT_OWNER_EMAIL')) {
    define('SPARK_ROOT_OWNER_EMAIL', 'dadudekc@gmail.com');
}

const SPARK_PAID_PLANS = ['paid', 'premium', 'pro'];
const SPARK_STAFF_ROLES = ['owner', 'admin'];

function spark_ent_storage_dir(): string {
    $dir = dirname(__DIR__) . '/.spark-auth';
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
        $htaccess = $dir . '/.htaccess';
        if (!is_file($htaccess)) {
            file_put_contents($htaccess, "Require all denied\n");
        }
    }
    return $dir;
}

function spark_ent_file(string $name): string {
    return spark_ent_storage_dir() . '/' . $name;
}

function spark_ent_read(string $name): array {
    $file = spark_ent_file($name);
    if (!is_file($file)) {
        return [];
    }
    $raw = file_get_contents($file);
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function spark_ent_write(string $name, array $data): void {
    $file = spark_ent_file($name);
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    chmod($file, 0600);
}

function spark_ent_normalize_email(string $email): string {
    return strtolower(trim($email));
}

function spark_ent_user_role(array $user): string {
    $stored = strtolower((string)($user['game_role'] ?? ''));
    if (in_array($stored, ['owner', 'admin', 'dev', 'moderator'], true)) {
        return $stored;
    }
    $email = spark_ent_normalize_email((string)($user['email'] ?? ''));
    if ($email === spark_ent_normalize_email(SPARK_ROOT_OWNER_EMAIL)) {
        return 'owner';
    }
    return 'player';
}

function spark_ent_user_is_staff(array $user): bool {
    return in_array(spark_ent_user_role($user), SPARK_STAFF_ROLES, true);
}

function spark_ent_user_plan(array $user): string {
    return strtolower((string)($user['spark_plan'] ?? 'free'));
}

function spark_ent_user_render_credits(array $user): int {
    return max(0, (int)($user['skymotion_render_credits'] ?? 0));
}

function spark_ent_user_has_paid_plan(array $user): bool {
    return in_array(spark_ent_user_plan($user), SPARK_PAID_PLANS, true);
}

function spark_ent_user_can_render(array $user): bool {
    if (spark_ent_user_is_staff($user)) {
        return true;
    }
    if (spark_ent_user_has_paid_plan($user)) {
        return true;
    }
    return spark_ent_user_render_credits($user) > 0;
}

function spark_ent_render_access(array $user): array {
    $staff = spark_ent_user_is_staff($user);
    $plan = spark_ent_user_plan($user);
    $credits = spark_ent_user_render_credits($user);
    $canRender = spark_ent_user_can_render($user);

    return [
        'can_render_video' => $canRender,
        'skymotion_access' => $canRender,
        'spark_plan' => $plan,
        'skymotion_render_credits' => $credits,
        'is_staff' => $staff,
        'is_owner' => spark_ent_user_role($user) === 'owner',
        'render_billing_mode' => $staff ? 'staff_free' : ($plan !== 'free' && spark_ent_user_has_paid_plan($user) ? 'subscription' : ($credits > 0 ? 'credits' : 'none')),
    ];
}

function spark_ent_current_session(): ?array {
    $token = $_COOKIE[SPARK_AUTH_COOKIE] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }

    $sessions = spark_ent_read('sessions.json');
    $session = $sessions[$token] ?? null;
    if (!is_array($session) || (int)($session['expires_at'] ?? 0) < time()) {
        unset($sessions[$token]);
        spark_ent_write('sessions.json', $sessions);
        return null;
    }

    $users = spark_ent_read('users.json');
    foreach ($users as $user) {
        if (!is_array($user)) {
            continue;
        }
        if (($user['id'] ?? '') === ($session['user_id'] ?? '')) {
            return ['token' => $token, 'session' => $session, 'user' => $user];
        }
    }

    return null;
}

function spark_ent_require_render_access(): array {
    $current = spark_ent_current_session();
    if (!$current) {
        spark_ent_json([
            'error' => 'auth_required',
            'message' => 'Sign in to generate SkyMotion previews.',
            'login_url' => '/spark-login/?redirect_to=' . rawurlencode('/'),
        ], 401);
    }

    if (!spark_ent_user_can_render($current['user'])) {
        spark_ent_json([
            'error' => 'payment_required',
            'message' => 'Subscribe or buy render credits to generate SkyMotion previews.',
            'subscribe_url' => '/spark-signup/',
            'billing_url' => '/api/spark-billing.php?action=checkout',
        ], 402);
    }

    return $current;
}

function spark_ent_consume_render_credit(string $userId): void {
    $users = spark_ent_read('users.json');
    foreach ($users as $index => $user) {
        if (!is_array($user) || (string)($user['id'] ?? '') !== $userId) {
            continue;
        }
        if (spark_ent_user_is_staff($user) || spark_ent_user_has_paid_plan($user)) {
            return;
        }
        $credits = spark_ent_user_render_credits($user);
        if ($credits <= 0) {
            spark_ent_json([
                'error' => 'payment_required',
                'message' => 'No render credits remaining.',
            ], 402);
        }
        $users[$index]['skymotion_render_credits'] = $credits - 1;
        $users[$index]['skymotion_last_render_at'] = gmdate('c');
        spark_ent_write('users.json', $users);
        return;
    }
}

function spark_ent_record_job(string $userId, string $jobId): void {
    if ($jobId === '') {
        return;
    }
    $jobs = spark_ent_read('skymotion_jobs.json');
    $jobs[$jobId] = [
        'user_id' => $userId,
        'created_at' => gmdate('c'),
    ];
    if (count($jobs) > 2000) {
        $jobs = array_slice($jobs, -2000, null, true);
    }
    spark_ent_write('skymotion_jobs.json', $jobs);
}

function spark_ent_verify_job_owner(string $userId, string $jobId): bool {
    if ($jobId === '') {
        return false;
    }
    $jobs = spark_ent_read('skymotion_jobs.json');
    $record = $jobs[$jobId] ?? null;
    if (!is_array($record)) {
        return false;
    }
    return (string)($record['user_id'] ?? '') === $userId;
}

function spark_ent_grant_plan(string $userId, string $plan): bool {
    $plan = strtolower(trim($plan));
    if (!in_array($plan, array_merge(['free'], SPARK_PAID_PLANS), true)) {
        return false;
    }
    $users = spark_ent_read('users.json');
    foreach ($users as $index => $user) {
        if (!is_array($user) || (string)($user['id'] ?? '') !== $userId) {
            continue;
        }
        $users[$index]['spark_plan'] = $plan;
        $users[$index]['spark_plan_updated_at'] = gmdate('c');
        spark_ent_write('users.json', $users);
        return true;
    }
    return false;
}

function spark_ent_add_credits(string $userId, int $amount): bool {
    if ($amount <= 0) {
        return false;
    }
    $users = spark_ent_read('users.json');
    foreach ($users as $index => $user) {
        if (!is_array($user) || (string)($user['id'] ?? '') !== $userId) {
            continue;
        }
        $current = spark_ent_user_render_credits($user);
        $users[$index]['skymotion_render_credits'] = $current + $amount;
        $users[$index]['skymotion_credits_updated_at'] = gmdate('c');
        spark_ent_write('users.json', $users);
        return true;
    }
    return false;
}

function spark_ent_json(array $payload, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}
