<?php
/**
 * Plugin Name: Emergence Character Generator
 * Description: Public Spark Protocol character generator demo for The Emergence.
 * Version: 0.2.0
 * Author: Dream.OS
 */

if (!defined('ABSPATH')) {
    exit;
}

function emergence_cg_protocol_path() {
    return plugin_dir_path(__FILE__) . 'assets/spark-protocol-v85-domain-key.json';
}

function emergence_cg_protocol_data() {
    static $data = null;

    if ($data !== null) {
        return $data;
    }

    $path = emergence_cg_protocol_path();
    if (!file_exists($path)) {
        return array();
    }

    $raw = file_get_contents($path);
    $decoded = json_decode($raw, true);
    $data = is_array($decoded) ? $decoded : array();

    return $data;
}

function emergence_cg_domains() {
    $data = emergence_cg_protocol_data();
    return isset($data['domains']) ? $data['domains'] : array('Titan', 'Velocity', 'Energy', 'Specter', 'Duality', 'Omni', 'Primal', 'Mind');
}

function emergence_cg_score_to_tier($score) {
    if ($score <= 5) return 1;
    if ($score <= 12) return 2;
    if ($score <= 18) return 3;
    if ($score <= 27) return 4;
    return 5;
}

function emergence_cg_score_domains($answers) {
    $domains = emergence_cg_domains();
    $data = emergence_cg_protocol_data();
    $key = isset($data['domain_key']) ? $data['domain_key'] : array();

    $scores = array();
    foreach ($domains as $domain) {
        $scores[$domain] = 0;
    }

    for ($q = 1; $q <= 28; $q++) {
        $letter = isset($answers[$q - 1]) ? strtoupper(sanitize_text_field($answers[$q - 1])) : '';
        if (!preg_match('/^[A-H]$/', $letter)) {
            continue;
        }

        if (!isset($key[(string) $q][$letter])) {
            continue;
        }

        $entry = $key[(string) $q][$letter];
        $domain = $entry[0];
        $points = intval($entry[1]);

        if (isset($scores[$domain])) {
            $scores[$domain] += $points;
        }
    }

    return $scores;
}

function emergence_cg_manifested_domains($scores) {
    $domains = emergence_cg_domains();
    $highest = 0;

    foreach ($scores as $score) {
        $highest = max($highest, intval($score));
    }

    $threshold = $highest * 0.25;
    $manifested = array();

    foreach ($domains as $domain) {
        $score = isset($scores[$domain]) ? intval($scores[$domain]) : 0;
        if ($score === $highest || $score >= $threshold) {
            $manifested[] = $domain;
        }
    }

    usort($manifested, function ($a, $b) use ($scores, $domains) {
        $sa = isset($scores[$a]) ? intval($scores[$a]) : 0;
        $sb = isset($scores[$b]) ? intval($scores[$b]) : 0;

        if ($sa !== $sb) {
            return $sb <=> $sa;
        }

        return array_search($a, $domains, true) <=> array_search($b, $domains, true);
    });

    return $manifested;
}

function emergence_cg_tiers($scores) {
    $tiers = array();
    foreach ($scores as $domain => $score) {
        $tiers[$domain] = emergence_cg_score_to_tier(intval($score));
    }
    return $tiers;
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

    $scores = emergence_cg_score_domains($answers);
    $tiers = emergence_cg_tiers($scores);
    $manifested = emergence_cg_manifested_domains($scores);

    $selected_powers = array();
    foreach ($manifested as $domain) {
        $tier = $tiers[$domain];
        $list = $powers[$domain];

        $pick_a = $list[abs(crc32($domain . ':' . $scores[$domain])) % count($list)];
        $selected_powers[] = array(
            'domain' => $domain,
            'power' => $pick_a,
            'tier' => $tier,
            'lead' => $domain === $manifested[0],
        );

        if ($tier >= 2) {
            $pick_b = $list[(abs(crc32($scores[$domain] . ':' . $domain)) + 2) % count($list)];
            if ($pick_b !== $pick_a) {
                $selected_powers[] = array(
                    'domain' => $domain,
                    'power' => $pick_b,
                    'tier' => max(1, $tier - 1),
                    'lead' => false,
                );
            }
        }
    }

    $power_count = count($selected_powers);
    $highest_tier = 0;
    $second_tier = 0;

    foreach ($manifested as $domain) {
        $tier = $tiers[$domain];
        if ($tier > $highest_tier) {
            $second_tier = $highest_tier;
            $highest_tier = $tier;
        } elseif ($tier > $second_tier) {
            $second_tier = $tier;
        }
    }

    $spark_signature = intval(round(70 + ($highest_tier * 2.5) + ($second_tier * 1) + $power_count));
    $combat_capability = min(100, max(10, ($highest_tier * 10) + ($second_tier * 8) + ($power_count * 3)));

    return array(
        'protocol_version' => 'Spark Protocol v8.5 domain generation',
        'answers_expected' => 28,
        'scores' => $scores,
        'tiers' => $tiers,
        'manifest_threshold' => max($scores) * 0.25,
        'manifested' => $manifested,
        'powers' => $selected_powers,
        'spark_signature' => $spark_signature,
        'combat_capability' => $combat_capability,
        'threat_class' => emergence_cg_threat_class($combat_capability),
        'cast' => emergence_cg_cast(count($manifested)),
    );
}

function emergence_cg_shortcode() {
    $letters = array(
        'A' => 'A — Omni / Titan / Energy path',
        'B' => 'B — Energy / Primal / Omni path',
        'C' => 'C — Velocity / Specter / Titan path',
        'D' => 'D — Titan / Velocity / Energy path',
        'E' => 'E — Primal / Specter / Omni path',
        'F' => 'F — Specter / Velocity / Primal path',
        'G' => 'G — Mind',
        'H' => 'H — Duality',
    );

    ob_start();
    ?>
    <section class="emergence-cg">
        <div class="ecg-hero">
            <p class="ecg-kicker">Spark Protocol v8.5 demo</p>
            <h1>Generate your Spark</h1>
            <p>The Emergence began as a machine for answering “Who would win?” and evolved into a world where the answer could include you.</p>
        </div>

        <form id="emergence-cg-form" class="ecg-form">
            <p class="ecg-note">Answer 28 domain prompts. The demo now uses the real Spark Protocol domain table, tier mapping, and 25% manifest gate.</p>

            <?php for ($i = 1; $i <= 28; $i++) : ?>
                <fieldset class="ecg-question">
                    <legend><?php echo esc_html('Q' . $i . ' — Choose your instinct.'); ?></legend>
                    <select name="q<?php echo esc_attr($i); ?>" required>
                        <?php foreach ($letters as $letter => $label) : ?>
                            <option value="<?php echo esc_attr($letter); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            <?php endfor; ?>

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
        '0.2.0'
    );

    wp_register_script(
        'emergence-cg-script',
        plugins_url('assets/emergence-cg.js', __FILE__),
        array(),
        '0.2.0',
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
        $clean[] = strtoupper(sanitize_text_field($value));
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
