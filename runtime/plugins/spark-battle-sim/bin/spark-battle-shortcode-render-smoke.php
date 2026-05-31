<?php

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../../../');
define('SPARK_BATTLE_SIM_TESTING', true);

$GLOBALS['spark_shortcodes'] = [];
$GLOBALS['spark_actions'] = [];

function plugin_dir_path($file) { return rtrim(dirname($file), '/\\') . '/'; }
function plugin_dir_url($file) { return 'http://example.test/wp-content/plugins/' . basename(dirname($file)) . '/'; }
function add_action($hook, $callback) { $GLOBALS['spark_actions'][$hook][] = $callback; }
function add_shortcode($tag, $callback) { $GLOBALS['spark_shortcodes'][$tag] = $callback; }
function wp_enqueue_style($handle, $src, $deps = [], $ver = false) {}
function wp_nonce_field($action, $name) { echo '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES) . '" value="test-nonce">'; }
function wp_verify_nonce($nonce, $action) { return true; }
function sanitize_text_field($value) { return is_scalar($value) ? trim((string) $value) : ''; }
function sanitize_file_name($filename) {
    $filename = is_scalar($filename) ? (string) $filename : '';
    $filename = strtolower($filename);
    $filename = preg_replace('/[^a-z0-9._-]+/', '-', $filename);
    return trim($filename, '-');
}
function wp_unslash($value) { return $value; }
function selected($selected, $current, $echo = true) {
    $out = ((string) $selected === (string) $current) ? ' selected="selected"' : '';
    if ($echo) { echo $out; }
    return $out;
}
function esc_html($value) { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function esc_attr($value) { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function esc_url($value) { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function wp_kses_post($value) { return (string) $value; }
function wpautop($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (stripos($value, '<p') !== false) {
        return $value;
    }
    return '<p>' . $value . '</p>';
}

putenv('SPARK_BATTLE_FIXED_RNG=1,2,3,4,5,87');

require_once __DIR__ . '/../spark-battle-sim.php';

if (!isset($GLOBALS['spark_shortcodes']['spark_battle_sim'])) {
    fwrite(STDERR, "SHORTCODE_REGISTERED=FAIL\n");
    exit(2);
}

require_once __DIR__ . '/../includes/CharacterRepository.php';
$repo = new Spark_Battle_CharacterRepository();
$all = $repo->all();

$slugs = [];
foreach ($all as $key => $item) {
    if (is_string($key)) {
        $slugs[] = $key;
    }
    if (is_array($item)) {
        foreach (['slug', 'id'] as $field) {
            if (!empty($item[$field]) && is_string($item[$field])) {
                $slugs[] = $item[$field];
            }
        }
    }
}

$slugs = array_values(array_unique($slugs));
if (count($slugs) < 2) {
    fwrite(STDERR, "SHORTCODE_SLUG_DISCOVERY=FAIL\n");
    exit(3);
}

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['spark_battle_nonce'] = 'test-nonce';
$_POST['fighter_a'] = $slugs[0];
$_POST['fighter_b'] = $slugs[1];

$html = call_user_func($GLOBALS['spark_shortcodes']['spark_battle_sim']);

echo "SHORTCODE_RENDER_SMOKE=PASS\n";
echo "SLUG_A={$slugs[0]}\n";
echo "SLUG_B={$slugs[1]}\n";
echo "HTML_BEGIN\n";
echo $html;
echo "\nHTML_END\n";

if (stripos($html, 'Result:') === false && stripos($html, 'left standing') === false) {
    fwrite(STDERR, "SHORTCODE_RESULT_VISIBLE=FAIL\n");
    exit(4);
}

$forbidden = ['SHOWWORK', 'Odds:', 'Roll:', 'committed ', 'A band', 'B band'];
foreach ($forbidden as $needle) {
    if (stripos($html, $needle) !== false) {
        fwrite(STDERR, "PLAYER_OUTPUT_LEAK={$needle}\n");
        exit(5);
    }
}

echo "PLAYER_OUTPUT_MATH_HIDDEN=PASS\n";
