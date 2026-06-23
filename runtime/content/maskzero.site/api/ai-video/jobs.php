<?php
declare(strict_types=1);

/**
 * Same-origin proxy: maskzero.site -> video-api.maskszero.site /v1/jobs.
 * Requires MaskZero login + paid studio access (owner/admin render free).
 */

require_once dirname(__DIR__) . '/spark-entitlements.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$backendBase = rtrim(getenv('AI_VIDEO_BACKEND_URL') ?: 'https://video-api.maskszero.site', '/');

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
        proxy_json(502, ['error' => 'invalid_backend_json', 'raw' => substr((string) $responseBody, 0, 500)]);
    }

    return ['code' => $httpCode, 'body' => $decoded];
}

function map_v1_status(string $status): string {
    return match ($status) {
        'complete', 'completed', 'succeeded' => 'succeeded',
        default => $status,
    };
}

function map_v1_progress(string $status): int {
    return match (map_v1_status($status)) {
        'queued' => 8,
        'running' => 55,
        'succeeded' => 100,
        'failed' => 100,
        default => 0,
    };
}

/** Normalize maskszero /v1/jobs payload for the studio client contract. */
function map_v1_job(array $decoded): array {
    $status = map_v1_status((string) ($decoded['status'] ?? 'unknown'));
    $videoUrl = $decoded['render_url'] ?? $decoded['thumbnail_url'] ?? null;

    return [
        'job_id' => (string) ($decoded['job_id'] ?? ''),
        'status' => $status,
        'progress' => map_v1_progress((string) ($decoded['status'] ?? 'unknown')),
        'video_url' => is_string($videoUrl) && $videoUrl !== '' ? $videoUrl : null,
        'error' => isset($decoded['error']) && is_string($decoded['error']) ? $decoded['error'] : null,
    ];
}

function build_v1_create_payload(string $raw): string {
    $incoming = json_decode($raw, true);
    if (!is_array($incoming)) {
        $incoming = [];
    }

    $payload = [
        'source' => 'maskzero.site',
        'event_type' => 'studio_render',
        'prompt' => (string) ($incoming['prompt'] ?? ''),
        'mode' => (string) ($incoming['mode'] ?? 'text_to_video'),
    ];

    if (isset($incoming['style']) && is_string($incoming['style']) && $incoming['style'] !== '') {
        $payload['style'] = $incoming['style'];
    }
    if (isset($incoming['duration']) && is_string($incoming['duration']) && $incoming['duration'] !== '') {
        $payload['duration'] = $incoming['duration'];
    }

    return json_encode($payload, JSON_UNESCAPED_SLASHES);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
    $current = spark_ent_require_render_access();
    $userId = (string) ($current['user']['id'] ?? '');

    $raw = file_get_contents('php://input') ?: '{}';
    $result = proxy_request('POST', $backendBase . '/v1/jobs', build_v1_create_payload($raw));

    if ($result['code'] < 400) {
        $mapped = map_v1_job($result['body']);
        $jobId = $mapped['job_id'];
        if ($jobId !== '') {
            spark_ent_consume_render_credit($userId);
            spark_ent_record_job($userId, $jobId);
        }
        proxy_json(200, $mapped);
    }

    $error = $result['body']['detail'] ?? $result['body']['error'] ?? 'backend_error';
    proxy_json($result['code'] >= 400 ? $result['code'] : 502, [
        'error' => is_string($error) ? $error : 'backend_error',
        'detail' => $result['body'],
    ]);
}

if ($method === 'GET') {
    $current = spark_ent_current_session();
    if (!$current) {
        proxy_json(401, [
            'error' => 'auth_required',
            'message' => 'Sign in to check render status.',
            'login_url' => '/spark-login/?redirect_to=' . rawurlencode('/studio/'),
        ]);
    }

    $jobId = isset($_GET['job_id']) ? trim((string) $_GET['job_id']) : '';
    if ($jobId === '' || !preg_match('/^[a-zA-Z0-9-]+$/', $jobId)) {
        proxy_json(400, ['error' => 'job_id_required']);
    }

    $userId = (string) ($current['user']['id'] ?? '');
    if (!spark_ent_verify_job_owner($userId, $jobId) && !spark_ent_user_is_staff($current['user'])) {
        proxy_json(403, [
            'error' => 'forbidden',
            'message' => 'This render job belongs to another account.',
        ]);
    }

    $result = proxy_request('GET', $backendBase . '/v1/jobs/' . rawurlencode($jobId));
    if ($result['code'] >= 400) {
        $error = $result['body']['detail'] ?? $result['body']['error'] ?? 'backend_error';
        proxy_json($result['code'], [
            'error' => is_string($error) ? $error : 'backend_error',
            'detail' => $result['body'],
        ]);
    }

    proxy_json(200, map_v1_job($result['body']));
}

proxy_json(405, ['error' => 'method_not_allowed']);
