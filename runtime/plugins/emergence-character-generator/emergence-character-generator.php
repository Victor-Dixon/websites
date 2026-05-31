<?php
/**
 * Plugin Name: Emergence Character Generator
 * Description: Public Spark Protocol-lite character generator demo for The Emergence.
 * Version: 0.1.0
 * Author: Dream.OS
 */

if (!defined('ABSPATH')) {
    exit;
}

function emergence_cg_domains() {
    return array('Titan', 'Velocity', 'Energy', 'Specter', 'Duality', 'Omni', 'Primal', 'Mind');
}

function emergence_cg_score_to_tier($score) {
    if ($score <= 5) return 1;
    if ($score <= 12) return 2;
    if ($score <= 18) return 3;
    if ($score <= 27) return 4;
    return 5;
}

function emergence_cg_domain_powers() {
    return array(
        'Titan' => array('Super Strength', 'Invulnerability', 'Density Control', 'Giant Size', 'Elasticity', 'Unstoppable Momentum'),
        'Velocity' => array('Super Speed', 'Flight', 'Enhanced Reflexes', 'Danger Sense', 'Wall-Crawling', 'Vibration Control'),
        'Energy' => array('Concussive Blasts', 'Pyrokinesis', 'Cryokinesis', 'Electrokinesis', 'Sonic Scream', 'Hydrokinesis'),
        'Specter' => array('Teleportation', 'Intangibility', 'Invisibility', 'Shrinking', 'Enhanced Senses', 'Portal Creation'),
        'Duality' => array('Hard Light', 'Laser Light', 'Energy Absorption', 'Shadow Control', 'Toxic Emission', 'Void Grasp'),
        'Omni' => array('Kinetic Manipulation', 'Force Fields', 'Healing Factor', 'Gravity Control', 'Magnetism', 'Duplication'),
        'Primal' => array('Shapeshifting', 'Nature Control', 'Weather Control', 'Animal Form', 'Adaptive Biology', 'Pheromone Control'),
        'Mind' => array('Telepathy', 'Mind Control', 'Telekinesis', 'Illusion', 'Psychic Assault', 'Psychic Defense'),
    );
}

function emergence_cg_cast($count) {
    if ($count <= 1) return 'Solo Spark';
    if ($count === 2) return 'Dual-Cast';
    if ($count <= 4) return 'Multi-Cast';
    return 'Wild-Cast';
}

function emergence_cg_threat_class($cc) {
    if ($cc <= 15) return 'Alpha';
    if ($cc <= 30) return 'Beta';
    if ($cc <= 50) return 'Gamma';
    if ($cc <= 75) return 'Delta';
    return 'Sigma';
}

function emergence_cg_generate($answers) {
    $domains = emergence_cg_domains();
    $powers = emergence_cg_domain_powers();

    $scores = array();
    foreach ($domains as $domain) {
        $scores[$domain] = 0;
    }

    foreach ($answers as $idx => $domain) {
        if (isset($scores[$domain])) {
            $scores[$domain] += (($idx % 7) === 0) ? 2 : 1;
        }
    }

    arsort($scores);
    $highest = max($scores);
    $threshold = $highest * 0.25;

    $manifested = array();
    foreach ($scores as $domain => $score) {
        if ($score === $highest || $score >= $threshold) {
            $manifested[] = $domain;
        }
    }

    $top_domain = $manifested[0];
    $top_score = $scores[$top_domain];
    $top_tier = emergence_cg_score_to_tier($top_score);

    $second_tier = 0;
    if (isset($manifested[1])) {
        $second_tier = emergence_cg_score_to_tier($scores[$manifested[1]]);
    }

    $selected_powers = array();
    foreach ($manifested as $domain) {
        $tier = emergence_cg_score_to_tier($scores[$domain]);
        $list = $powers[$domain];
        $pick_a = $list[abs(crc32($domain . ':' . $scores[$domain])) % count($list)];
        $pick_b = $list[(abs(crc32($scores[$domain] . ':' . $domain)) + 2) % count($list)];

        $selected_powers[] = array(
            'domain' => $domain,
            'power' => $pick_a,
            'tier' => $tier,
            'lead' => $domain === $top_domain,
        );

        if ($pick_b !== $pick_a && $tier >= 2) {
            $selected_powers[] = array(
                'domain' => $domain,
                'power' => $pick_b,
                'tier' => max(1, $tier - 1),
                'lead' => false,
            );
        }
    }

    $power_count = count($selected_powers);
    $signature = intval(round(70 + ($top_tier * 2.5) + ($second_tier * 1) + $power_count));
    $cc = min(100, max(10, ($top_tier * 10) + ($second_tier * 8) + ($power_count * 3)));

    return array(
        'scores' => $scores,
        'manifested' => $manifested,
        'powers' => $selected_powers,
        'spark_signature' => $signature,
        'combat_capability' => $cc,
        'threat_class' => emergence_cg_threat_class($cc),
        'cast' => emergence_cg_cast(count($manifested)),
    );
}

function emergence_cg_shortcode() {
    $domains = emergence_cg_domains();
    $nonce = wp_create_nonce('emergence_cg_nonce');

    ob_start();
    ?>
    <section class="emergence-cg" data-nonce="<?php echo esc_attr($nonce); ?>">
        <div class="ecg-hero">
            <p class="ecg-kicker">Build in public demo</p>
            <h1>Generate your Spark</h1>
            <p>The Emergence began as a machine for answering “Who would win?” and evolved into a world where the answer could include you.</p>
        </div>

        <form id="emergence-cg-form" class="ecg-form">
            <p class="ecg-note">Choose the instinct that fits you best for each prompt. This public demo uses a Spark-lite deterministic pass; the full rules engine comes next.</p>

            <?php
            $questions = array(
                'When pressure hits, what do you become?',
                'What kind of advantage feels most natural?',
                'What scares your enemies most?',
                'How do you survive the impossible?',
                'What kind of power would change your story?',
                'When the arena shifts, what do you trust?',
                'What does your legend feel like?'
            );

            foreach ($questions as $i => $question) :
            ?>
                <fieldset class="ecg-question">
                    <legend><?php echo esc_html(($i + 1) . '. ' . $question); ?></legend>
                    <select name="q<?php echo esc_attr($i); ?>" required>
                        <?php foreach ($domains as $domain) : ?>
                            <option value="<?php echo esc_attr($domain); ?>"><?php echo esc_html($domain); ?></option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit">Generate Spark</button>
        </form>

        <div id="emergence-cg-result" class="ecg-result" aria-live="polite"></div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('emergence_character_generator', 'emergence_cg_shortcode');

function emergence_cg_register_assets() {
    wp_register_style(
        'emergence-cg-style',
        plugins_url('assets/emergence-cg.css', __FILE__),
        array(),
        '0.1.0'
    );

    wp_register_script(
        'emergence-cg-script',
        plugins_url('assets/emergence-cg.js', __FILE__),
        array(),
        '0.1.0',
        true
    );

    wp_localize_script('emergence-cg-script', 'EmergenceCG', array(
        'endpoint' => esc_url_raw(rest_url('emergence/v1/generate')),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}

add_action('wp_enqueue_scripts', 'emergence_cg_register_assets');

function emergence_cg_enqueue_when_shortcode($posts) {
    if (empty($posts)) {
        return $posts;
    }

    foreach ($posts as $post) {
        if (isset($post->post_content) && has_shortcode($post->post_content, 'emergence_character_generator')) {
            wp_enqueue_style('emergence-cg-style');
            wp_enqueue_script('emergence-cg-script');
            break;
        }
    }

    return $posts;
}

add_filter('the_posts', 'emergence_cg_enqueue_when_shortcode');

function emergence_cg_rest_generate(WP_REST_Request $request) {
    $answers = $request->get_param('answers');
    if (!is_array($answers)) {
        return new WP_Error('bad_answers', 'answers must be an array', array('status' => 400));
    }

    $clean = array();
    foreach ($answers as $value) {
        $clean[] = sanitize_text_field($value);
    }

    return rest_ensure_response(emergence_cg_generate($clean));
}

add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/generate', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_rest_generate',
        'permission_callback' => '__return_true',
    ));
});
