<?php
declare(strict_types=1);

/**
 * SkyMotion billing: Stripe checkout + webhook, with payment-link fallback.
 */

require_once __DIR__ . '/spark-entitlements.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function billing_input(): array {
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

function billing_site_origin(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'maskszero.site');
    return $scheme . '://' . $host;
}

function billing_stripe_request(string $method, string $path, array $fields): array {
    $secret = getenv('SKYMOTION_STRIPE_SECRET_KEY') ?: getenv('STRIPE_SECRET_KEY') ?: '';
    if ($secret === '') {
        spark_ent_json(['ok' => false, 'message' => 'Stripe is not configured on this server.'], 503);
    }

    $ch = curl_init('https://api.stripe.com/v1/' . ltrim($path, '/'));
    if ($ch === false) {
        spark_ent_json(['ok' => false, 'message' => 'Stripe client failed to initialize.'], 502);
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_USERPWD => $secret . ':',
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_POSTFIELDS => http_build_query($fields),
    ]);

    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($body ?: '{}', true);
    if (!is_array($decoded)) {
        spark_ent_json(['ok' => false, 'message' => 'Invalid Stripe response.'], 502);
    }
    if ($code >= 400) {
        $message = (string)($decoded['error']['message'] ?? 'Stripe checkout failed.');
        spark_ent_json(['ok' => false, 'message' => $message], $code);
    }
    return $decoded;
}

function billing_checkout(): void {
    $current = spark_ent_current_session();
    if (!$current) {
        spark_ent_json([
            'ok' => false,
            'message' => 'Sign in before checkout.',
            'login_url' => '/spark-login/?redirect_to=' . rawurlencode('/'),
        ], 401);
    }

    $paymentLink = getenv('SKYMOTION_STRIPE_PAYMENT_LINK') ?: '';
    if ($paymentLink !== '' && !getenv('SKYMOTION_STRIPE_SECRET_KEY') && !getenv('STRIPE_SECRET_KEY')) {
        spark_ent_json([
            'ok' => true,
            'mode' => 'payment_link',
            'url' => $paymentLink,
        ]);
    }

    $priceId = getenv('SKYMOTION_STRIPE_PRICE_ID') ?: '';
    if ($priceId === '') {
        spark_ent_json([
            'ok' => false,
            'message' => 'Billing is not configured yet. Contact support.',
        ], 503);
    }

    $user = $current['user'];
    $origin = billing_site_origin();
    $mode = strtolower((string)(getenv('SKYMOTION_STRIPE_MODE') ?: 'subscription'));
    $lineMode = $mode === 'payment' ? 'payment' : 'subscription';

    $session = billing_stripe_request('POST', 'checkout/sessions', [
        'mode' => $lineMode,
        'success_url' => $origin . '/?billing=success',
        'cancel_url' => $origin . '/?billing=cancel',
        'client_reference_id' => (string)($user['id'] ?? ''),
        'customer_email' => (string)($user['email'] ?? ''),
        'line_items[0][price]' => $priceId,
        'line_items[0][quantity]' => 1,
        'metadata[user_id]' => (string)($user['id'] ?? ''),
        'metadata[product]' => 'skymotion',
    ]);

    spark_ent_json([
        'ok' => true,
        'mode' => 'stripe_checkout',
        'session_id' => (string)($session['id'] ?? ''),
        'url' => (string)($session['url'] ?? ''),
    ]);
}

function billing_status(): void {
    $current = spark_ent_current_session();
    if (!$current) {
        spark_ent_json(['ok' => true, 'logged_in' => false]);
    }

    $access = spark_ent_render_access($current['user']);
    spark_ent_json(array_merge([
        'ok' => true,
        'logged_in' => true,
        'user_id' => (string)($current['user']['id'] ?? ''),
        'email' => (string)($current['user']['email'] ?? ''),
    ], $access));
}

function billing_webhook(): void {
    $secret = getenv('SKYMOTION_STRIPE_WEBHOOK_SECRET') ?: getenv('STRIPE_WEBHOOK_SECRET') ?: '';
    if ($secret === '') {
        spark_ent_json(['ok' => false, 'message' => 'Webhook secret missing.'], 503);
    }

    $payload = file_get_contents('php://input') ?: '';
    $signature = (string)($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
    if ($payload === '' || $signature === '') {
        spark_ent_json(['ok' => false, 'message' => 'Missing webhook payload.'], 400);
    }

    $parts = [];
    foreach (explode(',', $signature) as $chunk) {
        [$key, $value] = array_pad(explode('=', trim($chunk), 2), 2, '');
        if ($key !== '' && $value !== '') {
            $parts[$key][] = $value;
        }
    }
    $timestamp = (string)($parts['t'][0] ?? '');
    $v1 = $parts['v1'][0] ?? '';
    if ($timestamp === '' || $v1 === '') {
        spark_ent_json(['ok' => false, 'message' => 'Invalid Stripe signature header.'], 400);
    }

    $signed = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
    if (!hash_equals($signed, (string)$v1)) {
        spark_ent_json(['ok' => false, 'message' => 'Webhook signature rejected.'], 400);
    }

    $event = json_decode($payload, true);
    if (!is_array($event)) {
        spark_ent_json(['ok' => false, 'message' => 'Invalid webhook JSON.'], 400);
    }

    $type = (string)($event['type'] ?? '');
    $object = $event['data']['object'] ?? null;
    if ($type === 'checkout.session.completed' && is_array($object)) {
        $userId = (string)($object['metadata']['user_id'] ?? $object['client_reference_id'] ?? '');
        if ($userId !== '') {
            $mode = (string)($object['mode'] ?? '');
            if ($mode === 'payment') {
                $pack = max(1, (int)(getenv('SKYMOTION_CREDITS_PER_PURCHASE') ?: 5));
                spark_ent_add_credits($userId, $pack);
            } else {
                spark_ent_grant_plan($userId, 'premium');
            }
        }
    }

    spark_ent_json(['ok' => true]);
}

$action = (string)($_GET['action'] ?? billing_input()['action'] ?? 'status');

if ($action === 'checkout') {
    billing_checkout();
}
if ($action === 'webhook') {
    billing_webhook();
}

billing_status();
