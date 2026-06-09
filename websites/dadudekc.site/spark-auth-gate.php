<?php
/**
 * Spark protected static route gate.
 * Keeps public pages public while requiring Spark Account login for gameplay/profile routes.
 */

require_once __DIR__ . '/wp-load.php';

$root = __DIR__;

$routes = [
    '/spark-dashboard/' => $root . '/spark-dashboard/index.html',
    '/spark-generator/' => $root . '/spark-generator/index.html',
    '/spark-battle/' => $root . '/spark-battle/index.html',
    '/meridian-map/' => $root . '/meridian-map/index.html',
    '/meridian-dispatch/' => $root . '/meridian-dispatch/index.html',
    '/news/' => $root . '/news/index.html',
    '/meridian-city/news/' => $root . '/meridian-city/news/index.html',
];

function dadudekc_spark_normalize_target($target): string {
    $target = parse_url((string) $target, PHP_URL_PATH) ?: '/';
    $target = '/' . ltrim($target, '/');
    if (substr($target, -1) !== '/') {
        $target .= '/';
    }
    return $target;
}

function dadudekc_user_has_saved_spark(): bool {
    if (!is_user_logged_in()) {
        return false;
    }

    $saved = get_user_meta(get_current_user_id(), 'spark_saved_dossiers_v1', true);
    return is_array($saved) && count($saved) > 0;
}

function dadudekc_target_requires_saved_spark(string $target): bool {
    return in_array($target, [
        '/meridian-dispatch/',
        '/meridian-map/',
        '/meridian-city/news/',
    ], true);
}

function dadudekc_inject_spark_rest_nonce($html) {
    if (!is_user_logged_in()) {
        return $html;
    }

    $nonce = wp_create_nonce('wp_rest');
    $payload = '<script id="spark-account-rest-nonce">window.SPARK_ACCOUNT=Object.assign({},window.SPARK_ACCOUNT||{},{restNonce:' . json_encode($nonce) . ',loggedIn:true});</script>';

    if (stripos($html, 'spark-account-rest-nonce') !== false) {
        return $html;
    }

    if (stripos($html, '</head>') !== false) {
        return str_ireplace('</head>', $payload . "\n</head>", $html);
    }

    if (stripos($html, '</body>') !== false) {
        return str_ireplace('</body>', $payload . "\n</body>", $html);
    }

    return $payload . $html;
}

$target = dadudekc_spark_normalize_target($_GET['target'] ?? '/');

if (!array_key_exists($target, $routes)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Spark route not found.';
    exit;
}

if (!is_user_logged_in()) {
    $login = '/spark-login/?redirect_to=' . rawurlencode($target);
    wp_safe_redirect(home_url($login), 302);
    exit;
}

if (dadudekc_target_requires_saved_spark($target) && !dadudekc_user_has_saved_spark()) {
    wp_safe_redirect(home_url('/spark-generator/?need_character=1'), 302);
    exit;
}

$file = $routes[$target];

if (!is_file($file)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Spark page not found.';
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$html = file_get_contents($file);
echo dadudekc_inject_spark_rest_nonce($html);
