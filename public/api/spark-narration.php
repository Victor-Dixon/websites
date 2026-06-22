<?php
declare(strict_types=1);

/**
 * MaskZero AI narration proxy — Ollama (local/VPS) + OpenAI fallback.
 * Authenticated users with paid access (owner/admin free) get server-side AI responses.
 */

require_once __DIR__ . '/spark-entitlements.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    spark_ent_json(['error' => 'method_not_allowed'], 405);
}

$current = spark_ent_require_render_access();
$user = $current['user'];

$raw = file_get_contents('php://input') ?: '{}';
$input = json_decode($raw, true);
if (!is_array($input)) {
    spark_ent_json(['error' => 'invalid_json'], 400);
}

$messages = $input['messages'] ?? null;
if (!is_array($messages) || count($messages) === 0) {
    spark_ent_json(['error' => 'messages_required'], 422);
}

$mode = strtolower((string)($input['mode'] ?? 'dm'));
$maxTokens = max(64, min(2048, (int)($input['max_tokens'] ?? 450)));
$temperature = max(0.0, min(1.5, (float)($input['temperature'] ?? 0.88)));

function narration_openai_messages(array $messages, int $maxTokens, float $temperature): ?array {
    $apiKey = getenv('MASKZERO_OPENAI_API_KEY') ?: getenv('OPENAI_API_KEY') ?: '';
    if ($apiKey === '') {
        return null;
    }

    $model = getenv('MASKZERO_OPENAI_MODEL') ?: 'gpt-4o-mini';
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    if ($ch === false) {
        return null;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ], JSON_UNESCAPED_SLASHES),
    ]);

    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($body ?: '{}', true);
    if (!is_array($decoded) || $code >= 400) {
        return null;
    }

    $text = trim((string)($decoded['choices'][0]['message']['content'] ?? ''));
    if ($text === '') {
        return null;
    }

    return [
        'text' => $text,
        'provider' => 'openai',
        'model' => $model,
    ];
}

function narration_ollama_messages(array $messages, int $maxTokens, float $temperature): ?array {
    $baseUrl = rtrim(getenv('OLLAMA_BASE_URL') ?: getenv('MASKZERO_OLLAMA_URL') ?: 'http://127.0.0.1:11434', '/');
    $model = getenv('OLLAMA_MODEL') ?: getenv('MASKZERO_OLLAMA_MODEL') ?: 'llama3.2:latest';

    $systemParts = [];
    $conversation = [];
    foreach ($messages as $message) {
        if (!is_array($message)) {
            continue;
        }
        $role = (string)($message['role'] ?? '');
        $content = trim((string)($message['content'] ?? ''));
        if ($content === '') {
            continue;
        }
        if ($role === 'system') {
            $systemParts[] = $content;
            continue;
        }
        $conversation[] = strtoupper($role) . ': ' . $content;
    }

    $prompt = implode("\n\n", $systemParts);
    if ($prompt !== '' && $conversation !== []) {
        $prompt .= "\n\n---\n\n";
    }
    $prompt .= implode("\n\n", $conversation);
    if ($prompt !== '') {
        $prompt .= "\n\nASSISTANT:";
    }

    $ch = curl_init($baseUrl . '/api/generate');
    if ($ch === false) {
        return null;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => $temperature,
                'num_predict' => $maxTokens,
            ],
        ], JSON_UNESCAPED_SLASHES),
    ]);

    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($body ?: '{}', true);
    if (!is_array($decoded) || $code >= 400) {
        return null;
    }

    $text = trim((string)($decoded['response'] ?? ''));
    if ($text === '') {
        return null;
    }

    return [
        'text' => $text,
        'provider' => 'ollama',
        'model' => $model,
    ];
}

$providerPref = strtolower((string)(getenv('MASKZERO_AI_PROVIDER') ?: 'ollama'));
$result = $providerPref === 'openai'
    ? narration_openai_messages($messages, $maxTokens, $temperature)
    : narration_ollama_messages($messages, $maxTokens, $temperature);

if ($result === null) {
    $result = narration_openai_messages($messages, $maxTokens, $temperature);
}
if ($result === null && $providerPref === 'openai') {
    $result = narration_ollama_messages($messages, $maxTokens, $temperature);
}

if ($result === null) {
    $model = getenv('OLLAMA_MODEL') ?: getenv('MASKZERO_OLLAMA_MODEL') ?: 'llama3.2:latest';
    spark_ent_json([
        'error' => 'provider_unavailable',
        'message' => 'AI model unavailable. On the VPS run: ollama pull ' . $model,
        'model' => $model,
    ], 503);
}

spark_ent_json([
    'ok' => true,
    'mode' => $mode,
    'text' => $result['text'],
    'provider' => $result['provider'],
    'model' => $result['model'],
    'user_id' => (string)($user['id'] ?? ''),
    'staff_free' => spark_ent_user_is_staff($user),
]);
