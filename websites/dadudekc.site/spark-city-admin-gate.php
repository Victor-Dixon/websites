<?php
/**
 * Spark city map admin route gate.
 * Requires login and manage_map_lore capability.
 */

require_once __DIR__ . '/wp-load.php';

$root = __DIR__;
$file = $root . '/spark-city-admin/index.html';

function dadudekc_spark_city_admin_inject_nonce($html) {
    if (!is_user_logged_in()) {
        return $html;
    }

    $nonce = wp_create_nonce('wp_rest');
    $uid = get_current_user_id();
    $caps = function_exists('spark_user_capabilities') ? spark_user_capabilities($uid) : [];
    $payload = '<script id="spark-city-admin-rest-nonce">window.SPARK_ACCOUNT=Object.assign({},window.SPARK_ACCOUNT||{},{restNonce:' .
        json_encode($nonce) .
        ',loggedIn:true,game_role:' . json_encode(spark_get_game_role($uid)) .
        ',capabilities:' . json_encode(array_values($caps)) .
        ',can_access_admin_panel:' . (spark_user_can_access_admin_panel($uid) ? 'true' : 'false') .
        '});</script>';

    if (stripos($html, 'spark-city-admin-rest-nonce') !== false) {
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

if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/spark-login/?redirect_to=' . rawurlencode('/spark-city-admin/')), 302);
    exit;
}

$uid = get_current_user_id();
$can_manage = function_exists('spark_user_can')
    && spark_user_can($uid, 'manage_map_lore');

if (!$can_manage && (!function_exists('spark_user_can_access_admin_panel') || !spark_user_can_access_admin_panel($uid))) {
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Access Denied</title></head><body style="font-family:system-ui,sans-serif;padding:2rem;background:#120a18;color:#fff4d6"><h1>Access Denied</h1><p>City Map Admin requires manage_map_lore capability.</p><p><a href="/spark-dashboard/" style="color:#2dd4ff">Return to Command Post</a></p></body></html>';
    exit;
}

if (!is_file($file)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'City map admin page not found.';
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$html = file_get_contents($file);
echo dadudekc_spark_city_admin_inject_nonce($html);
