<?php
/**
 * Spark mission admin route gate.
 * Requires login and manage_missions capability.
 */

require_once __DIR__ . '/wp-load.php';

$root = __DIR__;
$file = $root . '/spark-mission-admin/index.html';

function dadudekc_spark_mission_admin_inject_nonce($html) {
    if (!is_user_logged_in()) {
        return $html;
    }

    $nonce = wp_create_nonce('wp_rest');
    $uid = get_current_user_id();
    $payload = '<script id="spark-mission-admin-rest-nonce">window.SPARK_ACCOUNT=Object.assign({},window.SPARK_ACCOUNT||{},{restNonce:' .
        json_encode($nonce) .
        ',loggedIn:true,game_role:' . json_encode(spark_get_game_role($uid)) .
        ',can_access_admin_panel:' . (spark_user_can_access_admin_panel($uid) ? 'true' : 'false') .
        '});</script>';

    if (stripos($html, '</head>') !== false) {
        return str_ireplace('</head>', $payload . "\n</head>", $html);
    }
    return $payload . $html;
}

if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/spark-login/?redirect_to=' . rawurlencode('/spark-mission-admin/')), 302);
    exit;
}

$uid = get_current_user_id();
$can_manage = function_exists('spark_user_can')
    && spark_user_can($uid, 'manage_missions');

if (!$can_manage && (!function_exists('spark_user_can_access_admin_panel') || !spark_user_can_access_admin_panel($uid))) {
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Access Denied</title></head><body style="font-family:system-ui,sans-serif;padding:2rem;background:#120a18;color:#fff4d6"><h1>Access Denied</h1><p>Mission Admin requires manage_missions capability.</p><p><a href="/spark-dashboard/">Command Post</a></p></body></html>';
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
echo dadudekc_spark_mission_admin_inject_nonce(file_get_contents($file));
