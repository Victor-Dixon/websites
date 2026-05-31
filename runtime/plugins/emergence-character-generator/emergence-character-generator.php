<?php
/**
 * Plugin Name: Emergence Character Generator
 * Description: Public Spark Protocol v8.5 domain typing pass for The Emergence.
 * Version: 0.2.2
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
        $data = array();
        return $data;
    }

    $decoded = json_decode(file_get_contents($path), true);
    $data = is_array($decoded) ? $decoded : array();
    return $data;
}

function emergence_cg_domains() {
    $data = emergence_cg_protocol_data();
    return isset($data['domains']) && is_array($data['domains'])
        ? $data['domains']
        : array('Titan', 'Velocity', 'Energy', 'Specter', 'Duality', 'Omni', 'Primal', 'Mind');
}

function emergence_cg_score_to_tier($score) {
    $score = intval($score);
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

        $q_key = (string) $q;
        if (!isset($key[$q_key][$letter])) {
            continue;
        }

        $entry = $key[$q_key][$letter];
        $domain = $entry[0];
        $points = intval($entry[1]);

        if (isset($scores[$domain])) {
            $scores[$domain] += $points;
        }
    }

    return $scores;
}

function emergence_cg_tiers($scores) {
    $tiers = array();
    foreach ($scores as $domain => $score) {
        $tiers[$domain] = emergence_cg_score_to_tier($score);
    }
    return $tiers;
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

function emergence_cg_cast($count) {
    if ($count <= 1) return 'Solo Spark';
    if ($count === 2) return 'Dual-Cast';
    if ($count <= 4) return 'Multi-Cast';
    return 'Wild-Cast';
}

function emergence_cg_round_half_up($value) {
    return intval(floor(floatval($value) + 0.5));
}

function emergence_cg_spark_signature($highest_tier, $second_highest_tier, $power_count) {
    return emergence_cg_round_half_up(70 + ($highest_tier * 2.5) + ($second_highest_tier * 1) + $power_count);
}

function emergence_cg_profile_shape($highest_tier, $second_highest_tier, $manifest_count) {
    if ($manifest_count <= 1 && $highest_tier >= 4) {
        return 'Focused high-tier Spark: fewer manifested domains, stronger type identity.';
    }

    if ($manifest_count >= 5) {
        return 'Wide multi-domain Spark: broad manifestation, lower specialization pressure.';
    }

    if ($manifest_count >= 2) {
        return 'Hybrid Spark: multiple manifested domains with a readable lead type.';
    }

    return 'Focused Spark: one dominant manifested domain.';
}

function emergence_cg_generate($answers) {
    $scores = emergence_cg_score_domains($answers);
    $tiers = emergence_cg_tiers($scores);
    $manifested = emergence_cg_manifested_domains($scores);

    $highest_tier = 0;
    $second_tier = 0;

    foreach ($manifested as $domain) {
        $tier = isset($tiers[$domain]) ? intval($tiers[$domain]) : 0;
        if ($tier > $highest_tier) {
            $second_tier = $highest_tier;
            $highest_tier = $tier;
        } elseif ($tier > $second_tier) {
            $second_tier = $tier;
        }
    }

    $max_score = count($scores) ? max($scores) : 0;
    $manifest_threshold = $max_score * 0.25;

    $provisional_signature = emergence_cg_spark_signature($highest_tier, $second_tier, 0);
    $provisional_cc = min(100, max(10, ($highest_tier * 10) + ($second_tier * 8)));

    return array(
        'protocol_version' => 'Spark Protocol v8.5 domain typing pass',
        'answers_expected' => 28,
        'phase' => 'domain_typing',
        'scores' => $scores,
        'tiers' => $tiers,
        'manifest_threshold' => $manifest_threshold,
        'manifested' => $manifested,
        'lead_domain' => isset($manifested[0]) ? $manifested[0] : null,
        'profile_shape' => emergence_cg_profile_shape($highest_tier, $second_tier, count($manifested)),
        'provisional_spark_signature' => $provisional_signature,
        'provisional_combat_capability' => $provisional_cc,
        'power_selection_status' => 'locked_until_flavor_pass',
        'powers' => array(),
        'next_phase' => array(
            'name' => 'flavor_power_selection',
            'questions' => 'Q29-Q68',
            'description' => 'Flavor questions select actual sub-affinities/powers inside manifested domains.',
        ),
        'cast' => emergence_cg_cast(count($manifested)),
    );
}

function emergence_cg_shortcode() {
    $choices = array(
        'A' => array('label' => 'Shape the whole field', 'hint' => 'systems, force, adaptation, control'),
        'B' => array('label' => 'Become the pressure', 'hint' => 'energy, primal instinct, momentum'),
        'C' => array('label' => 'Move before the world reacts', 'hint' => 'speed, stealth, precision'),
        'D' => array('label' => 'Endure and overpower', 'hint' => 'strength, impact, direct conflict'),
        'E' => array('label' => 'Evolve through the impossible', 'hint' => 'growth, survival, transformation'),
        'F' => array('label' => 'Disappear, redirect, or outlast', 'hint' => 'specter movement, evasion, misdirection'),
        'G' => array('label' => 'Win through mind and perception', 'hint' => 'psychic force, strategy, influence'),
        'H' => array('label' => 'Split the rules in two', 'hint' => 'duality, contradiction, light/dark tension'),
    );

    $questions = array(
        'When danger arrives first, what instinct takes over?',
        'What kind of power would feel natural in your hands?',
        'How do you win when the odds are unfair?',
        'What would enemies misunderstand about you?',
        'What part of you refuses to break?',
        'How do you move through a hostile world?',
        'What kind of battlefield gives you the advantage?',
        'What do you become when you stop holding back?',
        'What kind of ally would trust you most?',
        'What kind of enemy would fear you most?',
        'What do you protect first?',
        'What kind of sacrifice would you accept?',
        'How do you recover from defeat?',
        'What kind of legend follows you?',
        'What would your power look like from a distance?',
        'What would your power feel like up close?',
        'How do you handle chaos?',
        'How do you handle control?',
        'What kind of secret would your origin hide?',
        'What kind of weakness keeps you human?',
        'What makes your victories dangerous?',
        'What makes your losses meaningful?',
        'What kind of arena changes everything?',
        'What do you do when the fight becomes personal?',
        'What kind of upgrade would tempt you?',
        'What part of your power should never be pushed too far?',
        'What does your Spark want?',
        'What does your Spark cost?'
    );

    ob_start();
    ?>
    <section class="emergence-cg">
        <div class="ecg-hero">
            <p class="ecg-kicker">Spark Protocol v8.5 public demo</p>
            <h1>Run your Spark Type Scan</h1>
            <p class="ecg-thesis">The Emergence began as a machine for answering “Who would win?” and evolved into a world where the answer could include you.</p>
            <div class="ecg-trust-row">
                <span>Deterministic scoring</span>
                <span>28-question domain table</span>
                <span>25% manifest gate</span>
                <span>No random rolls yet</span>
                <span>Powers locked until flavor pass</span>
            </div>
        </div>

        <div class="ecg-explainer">
            <h2>How this pass works</h2>
            <p>
                Each answer maps to one Spark domain using the protocol table. Most answers are worth 1 point;
                some are worth 2. Each domain has exactly three 2-point answers.
            </p>
            <p>
                Q1-Q28 produces domain scores, tiers, manifested domains, and profile shape.
                It does not auto-select powers. Powers require Q29-Q68 flavor scoring.
            </p>
        </div>

        <form id="emergence-cg-form" class="ecg-form">
            <div class="ecg-progress">
                <span id="ecg-progress-label">0 / 28 answered</span>
                <div class="ecg-progress-bar"><span id="ecg-progress-fill"></span></div>
            </div>

            <?php for ($i = 1; $i <= 28; $i++) : ?>
                <fieldset class="ecg-question">
                    <legend><?php echo esc_html('Q' . $i . ' — ' . $questions[$i - 1]); ?></legend>
                    <select name="q<?php echo esc_attr($i); ?>" required>
                        <option value="">Choose one...</option>
                        <?php foreach ($choices as $letter => $choice) : ?>
                            <option value="<?php echo esc_attr($letter); ?>">
                                <?php echo esc_html($letter . ' — ' . $choice['label'] . ' (' . $choice['hint'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            <?php endfor; ?>

            <button type="submit">Run Spark Type Scan</button>
        </form>

        <div id="emergence-cg-result" class="ecg-result" aria-live="polite">
            <p class="ecg-empty">Your Spark type scan will appear here.</p>
        </div>

        <div class="ecg-proof-note">
            <h2>What is verified right now?</h2>
            <ul>
                <li>Q1-Q28 domain table scoring is active.</li>
                <li>Domain scores convert into tiers.</li>
                <li>Manifested domains use the 25% highest-score gate.</li>
                <li>Actual power selection is locked until the Q29-Q68 flavor pass.</li>
            </ul>
        </div>
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
        '0.2.2'
    );

    wp_register_script(
        'emergence-cg-script',
        plugins_url('assets/emergence-cg.js', __FILE__),
        array(),
        '0.2.2',
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
    nocache_headers();
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

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
