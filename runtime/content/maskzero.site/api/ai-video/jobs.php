<?php
declare(strict_types=1);

/**
 * Same-origin HTTPS proxy: maskszero.site -> Dream.OS VPS Fal render API.
 * Requires MaskZero login + paid SkyMotion access (owner/admin render free).
 */

require_once dirname(__DIR__) . '/spark-entitlements.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$backendBase = getenv('AI_VIDEO_BACKEND_URL') ?: 'http://2.25.64.233';

function proxy_json(int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function proxy_request(string $method, string $url, ?string $body = null): array {
    $ch = curl_init($url);
    if ($ch === false) {
        proxy_json(502, ['error' => 'curl_init_failed']);
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $responseBody = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($responseBody === false) {
        proxy_json(502, ['error' => 'backend_unreachable', 'detail' => $curlError]);
    }

    $decoded = json_decode($responseBody, true);
    if (!is_array($decoded)) {
        proxy_json(502, ['error' => 'invalid_backend_json', 'raw' => substr($responseBody, 0, 500)]);
    }

    return ['code' => $httpCode, 'body' => $decoded];
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    $current = spark_ent_require_render_access();
    $userId = (string)($current['user']['id'] ?? '');

    $raw = file_get_contents('php://input') ?: '{}';
    $result = proxy_request('POST', rtrim($backendBase, '/') . '/api/render/jobs', $raw);

    if ($result['code'] < 400) {
        $jobId = (string)($result['body']['job_id'] ?? '');
        if ($jobId !== '') {
            spark_ent_consume_render_credit($userId);
            spark_ent_record_job($userId, $jobId);
        }
    }

    proxy_json($result['code'] >= 400 ? $result['code'] : 200, $result['body']);
}

if ($method === 'GET') {
    $current = spark_ent_current_session();
    if (!$current) {
        proxy_json(401, [
            'error' => 'auth_required',
            'message' => 'Sign in to check render status.',
            'login_url' => '/spark-login/?redirect_to=' . rawurlencode('/'),
        ]);
    }

    $jobId = isset($_GET['job_id']) ? trim((string) $_GET['job_id']) : '';
    if ($jobId === '' || !preg_match('/^[a-zA-Z0-9-]+$/', $jobId)) {
        proxy_json(400, ['error' => 'job_id_required']);
    }

    $userId = (string)($current['user']['id'] ?? '');
    if (!spark_ent_verify_job_owner($userId, $jobId) && !spark_ent_user_is_staff($current['user'])) {
        proxy_json(403, [
            'error' => 'forbidden',
            'message' => 'This render job belongs to another account.',
        ]);
    }

    $result = proxy_request('GET', rtrim($backendBase, '/') . '/api/render/jobs/' . rawurlencode($jobId));
    proxy_json($result['code'] >= 400 ? $result['code'] : 200, $result['body']);
}

proxy_json(405, ['error' => 'method_not_allowed']);
