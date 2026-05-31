<?php
/**
 * Plugin Name: Spark Battle Sim
 * Description: Cinematic Spark Protocol battle simulator shortcode.
 * Version: 0.1.0
 * Author: Dadudekc
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SPARK_BATTLE_SIM_DIR', plugin_dir_path(__FILE__));
define('SPARK_BATTLE_SIM_URL', plugin_dir_url(__FILE__));

require_once SPARK_BATTLE_SIM_DIR . 'includes/CharacterRepository.php';
require_once SPARK_BATTLE_SIM_DIR . 'includes/ArenaRoller.php';
require_once SPARK_BATTLE_SIM_DIR . 'includes/BattleEngine.php';
require_once SPARK_BATTLE_SIM_DIR . 'includes/StoryRenderer.php';

function spark_battle_sim_enqueue_assets() {
    wp_enqueue_style(
        'spark-battle-sim-css',
        SPARK_BATTLE_SIM_URL . 'assets/battle.css',
        array(),
        '0.1.0'
    );
}
add_action('wp_enqueue_scripts', 'spark_battle_sim_enqueue_assets');

function spark_battle_sim_shortcode() {
    $repo = new Spark_Battle_CharacterRepository();
    $engine = new Spark_Battle_BattleEngine($repo);

    $characters = $repo->all();
    $result = null;
    $error = null;

    if (
        isset($_POST['spark_battle_nonce']) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['spark_battle_nonce'])), 'spark_battle_run')
    ) {
        $fighter_a = sanitize_text_field(wp_unslash($_POST['fighter_a'] ?? ''));
        $fighter_b = sanitize_text_field(wp_unslash($_POST['fighter_b'] ?? ''));

        if (!$fighter_a || !$fighter_b) {
            $error = 'Choose two fighters.';
        } elseif ($fighter_a === $fighter_b) {
            $error = 'Choose two different fighters.';
        } else {
            try {
                $result = $engine->run($fighter_a, $fighter_b);
            } catch (Throwable $e) {
                if (defined('SPARK_BATTLE_SIM_TESTING') && SPARK_BATTLE_SIM_TESTING) {
                    throw $e;
                }
                $error = 'Battle could not start.';
            }
        }
    }

    ob_start();
    ?>
    <div class="spark-battle-shell">
        <h2>Spark Protocol Battle Arena</h2>

        <form method="post" class="spark-battle-form">
            <?php wp_nonce_field('spark_battle_run', 'spark_battle_nonce'); ?>

            <label>
                Fighter A
                <select name="fighter_a">
                    <?php foreach ($characters as $slug => $character): ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($slug, 'the-victor'); ?>>
                            <?php echo esc_html($character['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <span class="spark-vs">vs</span>

            <label>
                Fighter B
                <select name="fighter_b">
                    <?php foreach ($characters as $slug => $character): ?>
                        <option value="<?php echo esc_attr($slug); ?>" <?php selected($slug, 'captain-cap-wilson'); ?>>
                            <?php echo esc_html($character['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="submit">Start Battle</button>
        </form>

        <?php if ($error): ?>
            <p class="spark-battle-error"><?php echo esc_html($error); ?></p>
        <?php endif; ?>

        <?php if ($result): ?>
            <section class="spark-battle-result">
                <h3><?php echo esc_html($result['title']); ?></h3>
                <p><strong>Arena:</strong> <?php echo esc_html($result['arena']['summary']); ?></p>
                <p><strong>Result:</strong> <?php echo esc_html($result['winner']['name']); ?> is left standing.</p>
                <div class="spark-story">
                    <?php echo wp_kses_post(wpautop($result['story'])); ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('spark_battle_sim', 'spark_battle_sim_shortcode');
