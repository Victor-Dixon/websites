<?php
/**
 * Plugin Name: Emergence Character Generator
 * Description: Public Spark Protocol v8.5 two-pass character generator for The Emergence.
 * Version: 0.5.8
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
    if ($data !== null) return $data;

    $path = emergence_cg_protocol_path();
    if (!file_exists($path)) {
        $data = array();
        return $data;
    }

    $decoded = json_decode(file_get_contents($path), true);
    $data = is_array($decoded) ? $decoded : array();
    return $data;
}


function emergence_cg_question_bank() {
    $path = plugin_dir_path(__FILE__) . 'assets/protocol-v85-question-bank.json';
    if (!file_exists($path)) {
        return array('domain_questions' => array(), 'flavor_questions' => array());
    }

    $decoded = json_decode(file_get_contents($path), true);
    if (!is_array($decoded)) {
        return array('domain_questions' => array(), 'flavor_questions' => array());
    }

    return $decoded;
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

function emergence_cg_round_half_up($value) {
    return intval(floor(floatval($value) + 0.5));
}

function emergence_cg_spark_signature($highest_tier, $second_highest_tier, $power_count) {
    return emergence_cg_round_half_up(70 + ($highest_tier * 2.5) + ($second_highest_tier * 1) + $power_count);
}

function emergence_cg_cast($count) {
    if ($count <= 1) return 'Solo Spark';
    if ($count === 2) return 'Dual-Cast';
    if ($count <= 4) return 'Multi-Cast';
    return 'Wild-Cast';
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

function emergence_cg_domain_key() {
    $data = emergence_cg_protocol_data();
    return isset($data['domain_key']) ? $data['domain_key'] : array();
}

function emergence_cg_score_domains($answers) {
    $domains = emergence_cg_domains();
    $key = emergence_cg_domain_key();

    $scores = array();
    foreach ($domains as $domain) $scores[$domain] = 0;

    for ($q = 1; $q <= 28; $q++) {
        $letter = isset($answers[$q - 1]) ? strtoupper(sanitize_text_field($answers[$q - 1])) : '';
        if (!preg_match('/^[A-H]$/', $letter)) continue;

        $q_key = (string) $q;
        if (!isset($key[$q_key][$letter])) continue;

        $entry = $key[$q_key][$letter];
        $domain = $entry[0];
        $points = intval($entry[1]);

        if (isset($scores[$domain])) $scores[$domain] += $points;
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
    foreach ($scores as $score) $highest = max($highest, intval($score));

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
        if ($sa !== $sb) return $sb <=> $sa;
        return array_search($a, $domains, true) <=> array_search($b, $domains, true);
    });

    return $manifested;
}

function emergence_cg_flavor_key() {
    return array(
        29 => array('A'=>'Invulnerability','B'=>'Density Control','C'=>'Giant Size','D'=>'Elasticity','E'=>'Unstoppable Momentum','F'=>'Super Strength'),
        30 => array('A'=>'Super Strength','B'=>'Giant Size','C'=>'Elasticity','D'=>'Invulnerability','E'=>'Density Control','F'=>'Unstoppable Momentum'),
        31 => array('A'=>'Super Strength','B'=>'Invulnerability','C'=>'Giant Size','D'=>'Elasticity','E'=>'Unstoppable Momentum','F'=>'Density Control'),
        32 => array('A'=>'Invulnerability','B'=>'Density Control','C'=>'Elasticity','D'=>'Giant Size','E'=>'Unstoppable Momentum','F'=>'Super Strength'),
        33 => array('A'=>'Super Strength','B'=>'Giant Size','C'=>'Elasticity','D'=>'Unstoppable Momentum','E'=>'Invulnerability','F'=>'Density Control'),

        34 => array('A'=>'Super Speed','B'=>'Flight','C'=>'Enhanced Reflexes','D'=>'Danger Sense','E'=>'Wall-Crawling','F'=>'Vibration Control'),
        35 => array('A'=>'Enhanced Reflexes','B'=>'Flight','C'=>'Danger Sense','D'=>'Super Speed','E'=>'Wall-Crawling','F'=>'Vibration Control'),
        36 => array('A'=>'Super Speed','B'=>'Flight','C'=>'Enhanced Reflexes','D'=>'Danger Sense','E'=>'Wall-Crawling','F'=>'Vibration Control'),
        37 => array('A'=>'Super Speed','B'=>'Flight','C'=>'Enhanced Reflexes','D'=>'Danger Sense','E'=>'Wall-Crawling','F'=>'Vibration Control'),
        38 => array('A'=>'Super Speed','B'=>'Flight','C'=>'Enhanced Reflexes','D'=>'Danger Sense','E'=>'Wall-Crawling','F'=>'Vibration Control'),

        39 => array('A'=>'Pyrokinesis','B'=>'Cryokinesis','C'=>'Concussive Blasts','D'=>'Electrokinesis','E'=>'Sonic Scream','F'=>'Hydrokinesis'),
        40 => array('A'=>'Pyrokinesis','B'=>'Cryokinesis','C'=>'Concussive Blasts','D'=>'Sonic Scream','E'=>'Hydrokinesis','F'=>'Electrokinesis'),
        41 => array('A'=>'Concussive Blasts','B'=>'Pyrokinesis','C'=>'Cryokinesis','D'=>'Electrokinesis','E'=>'Sonic Scream','F'=>'Hydrokinesis'),
        42 => array('A'=>'Pyrokinesis','B'=>'Cryokinesis','C'=>'Concussive Blasts','D'=>'Electrokinesis','E'=>'Sonic Scream','F'=>'Hydrokinesis'),
        43 => array('A'=>'Pyrokinesis','B'=>'Concussive Blasts','C'=>'Cryokinesis','D'=>'Electrokinesis','E'=>'Sonic Scream','F'=>'Hydrokinesis'),

        44 => array('A'=>'Portal Creation','B'=>'Intangibility','C'=>'Invisibility','D'=>'Shrinking','E'=>'Enhanced Senses','F'=>'Teleportation'),
        45 => array('A'=>'Enhanced Senses','B'=>'Invisibility','C'=>'Teleportation','D'=>'Portal Creation','E'=>'Shrinking','F'=>'Intangibility'),
        46 => array('A'=>'Intangibility','B'=>'Teleportation','C'=>'Shrinking','D'=>'Invisibility','E'=>'Enhanced Senses','F'=>'Portal Creation'),
        47 => array('A'=>'Intangibility','B'=>'Shrinking','C'=>'Invisibility','D'=>'Portal Creation','E'=>'Enhanced Senses','F'=>'Teleportation'),
        48 => array('A'=>'Intangibility','B'=>'Shrinking','C'=>'Portal Creation','D'=>'Enhanced Senses','E'=>'Teleportation','F'=>'Invisibility'),

        49 => array('A'=>'Laser Light','B'=>'Energy Absorption','C'=>'Shadow Control','D'=>'Toxic Emission','E'=>'Void Grasp','F'=>'Hard Light'),
        50 => array('A'=>'Energy Absorption','B'=>'Toxic Emission','C'=>'Void Grasp','D'=>'Hard Light','E'=>'Laser Light','F'=>'Shadow Control'),
        51 => array('A'=>'Shadow Control','B'=>'Toxic Emission','C'=>'Void Grasp','D'=>'Hard Light','E'=>'Laser Light','F'=>'Energy Absorption'),
        52 => array('A'=>'Toxic Emission','B'=>'Void Grasp','C'=>'Hard Light','D'=>'Laser Light','E'=>'Energy Absorption','F'=>'Shadow Control'),
        53 => array('A'=>'Void Grasp','B'=>'Hard Light','C'=>'Laser Light','D'=>'Energy Absorption','E'=>'Shadow Control','F'=>'Toxic Emission'),

        54 => array('A'=>'Force Fields','B'=>'Healing Factor','C'=>'Gravity Control','D'=>'Magnetism','E'=>'Duplication','F'=>'Kinetic Manipulation'),
        55 => array('A'=>'Force Fields','B'=>'Healing Factor','C'=>'Gravity Control','D'=>'Kinetic Manipulation','E'=>'Magnetism','F'=>'Duplication'),
        56 => array('A'=>'Duplication','B'=>'Gravity Control','C'=>'Kinetic Manipulation','D'=>'Force Fields','E'=>'Magnetism','F'=>'Healing Factor'),
        57 => array('A'=>'Healing Factor','B'=>'Gravity Control','C'=>'Force Fields','D'=>'Magnetism','E'=>'Duplication','F'=>'Kinetic Manipulation'),
        58 => array('A'=>'Force Fields','B'=>'Duplication','C'=>'Healing Factor','D'=>'Gravity Control','E'=>'Kinetic Manipulation','F'=>'Magnetism'),

        59 => array('A'=>'Animal Form','B'=>'Nature Control','C'=>'Adaptive Biology','D'=>'Weather Control','E'=>'Pheromone Control','F'=>'Shapeshifting'),
        60 => array('A'=>'Adaptive Biology','B'=>'Weather Control','C'=>'Animal Form','D'=>'Pheromone Control','E'=>'Shapeshifting','F'=>'Nature Control'),
        61 => array('A'=>'Adaptive Biology','B'=>'Nature Control','C'=>'Animal Form','D'=>'Shapeshifting','E'=>'Pheromone Control','F'=>'Weather Control'),
        62 => array('A'=>'Nature Control','B'=>'Pheromone Control','C'=>'Animal Form','D'=>'Weather Control','E'=>'Adaptive Biology','F'=>'Shapeshifting'),
        63 => array('A'=>'Shapeshifting','B'=>'Nature Control','C'=>'Adaptive Biology','D'=>'Pheromone Control','E'=>'Animal Form','F'=>'Weather Control'),

        64 => array('A'=>'Telepathy','B'=>'Illusion','C'=>'Psychic Assault','D'=>'Psychic Defense','E'=>'Telekinesis','F'=>'Mind Control'),
        65 => array('A'=>'Telepathy','B'=>'Mind Control','C'=>'Psychic Defense','D'=>'Telekinesis','E'=>'Illusion','F'=>'Psychic Assault'),
        66 => array('A'=>'Psychic Assault','B'=>'Illusion','C'=>'Mind Control','D'=>'Telekinesis','E'=>'Telepathy','F'=>'Psychic Defense'),
        67 => array('A'=>'Mind Control','B'=>'Illusion','C'=>'Psychic Assault','D'=>'Telepathy','E'=>'Telekinesis','F'=>'Psychic Defense'),
        68 => array('A'=>'Mind Control','B'=>'Psychic Defense','C'=>'Illusion','D'=>'Telepathy','E'=>'Psychic Assault','F'=>'Telekinesis'),
    );
}

function emergence_cg_flavor_block_domain() {
    $map = array();
    foreach (range(29, 33) as $q) $map[$q] = 'Titan';
    foreach (range(34, 38) as $q) $map[$q] = 'Velocity';
    foreach (range(39, 43) as $q) $map[$q] = 'Energy';
    foreach (range(44, 48) as $q) $map[$q] = 'Specter';
    foreach (range(49, 53) as $q) $map[$q] = 'Duality';
    foreach (range(54, 58) as $q) $map[$q] = 'Omni';
    foreach (range(59, 63) as $q) $map[$q] = 'Primal';
    foreach (range(64, 68) as $q) $map[$q] = 'Mind';
    return $map;
}

function emergence_cg_subaffinities() {
    return array(
        'Titan' => array('Super Strength','Invulnerability','Density Control','Giant Size','Elasticity','Unstoppable Momentum'),
        'Velocity' => array('Super Speed','Flight','Enhanced Reflexes','Danger Sense','Wall-Crawling','Vibration Control'),
        'Energy' => array('Concussive Blasts','Pyrokinesis','Cryokinesis','Electrokinesis','Sonic Scream','Hydrokinesis'),
        'Specter' => array('Teleportation','Intangibility','Invisibility','Shrinking','Enhanced Senses','Portal Creation'),
        'Duality' => array('Hard Light','Laser Light','Energy Absorption','Shadow Control','Toxic Emission','Void Grasp'),
        'Omni' => array('Kinetic Manipulation','Force Fields','Healing Factor','Gravity Control','Magnetism','Duplication'),
        'Primal' => array('Shapeshifting','Nature Control','Weather Control','Animal Form','Adaptive Biology','Pheromone Control'),
        'Mind' => array('Telepathy','Mind Control','Telekinesis','Illusion','Psychic Assault','Psychic Defense'),
    );
}

function emergence_cg_score_flavor($flavor_answers, $manifested_domains) {
    $flavor_key = emergence_cg_flavor_key();
    $block_domain = emergence_cg_flavor_block_domain();
    $subaffinities = emergence_cg_subaffinities();

    $vectors = array();
    foreach ($manifested_domains as $domain) {
        $vectors[$domain] = array();
        foreach ($subaffinities[$domain] as $sub) {
            $vectors[$domain][$sub] = 0;
        }
    }

    for ($q = 29; $q <= 68; $q++) {
        if (!isset($block_domain[$q])) continue;
        $domain = $block_domain[$q];

        if (!in_array($domain, $manifested_domains, true)) {
            continue;
        }

        $letter = isset($flavor_answers[$q]) ? strtoupper(sanitize_text_field($flavor_answers[$q])) : '';
        if (!preg_match('/^[A-F]$/', $letter)) continue;
        if (!isset($flavor_key[$q][$letter])) continue;

        $sub = $flavor_key[$q][$letter];
        if (isset($vectors[$domain][$sub])) {
            $vectors[$domain][$sub] += 1;
        }
    }

    return $vectors;
}

function emergence_cg_select_powers($domain, $vector, $tier) {
    $subaffinities = emergence_cg_subaffinities();
    $order = $subaffinities[$domain];

    $qualifying = array();
    foreach ($vector as $sub => $score) {
        if (intval($score) >= 2) {
            $qualifying[$sub] = intval($score);
        }
    }

    if (empty($qualifying)) {
        $best = $order[0];
        $best_score = -1;
        foreach ($order as $sub) {
            $score = isset($vector[$sub]) ? intval($vector[$sub]) : 0;
            if ($score > $best_score) {
                $best = $sub;
                $best_score = $score;
            }
        }
        return array(array('domain' => $domain, 'power' => $best, 'tier' => $tier, 'lead' => true, 'selection' => 'latent_fallback'));
    }

    arsort($qualifying);
    $top_score = max($qualifying);
    $leads = array();
    foreach ($order as $sub) {
        if (isset($qualifying[$sub]) && $qualifying[$sub] === $top_score) {
            $leads[] = $sub;
        }
    }

    $powers = array();
    if (count($leads) >= 2) {
        foreach (array_slice($leads, 0, 2) as $sub) {
            $powers[] = array('domain' => $domain, 'power' => $sub, 'tier' => $tier, 'lead' => true, 'selection' => 'co_lead');
        }
        return $powers;
    }

    $lead = $leads[0];
    $powers[] = array('domain' => $domain, 'power' => $lead, 'tier' => $tier, 'lead' => true, 'selection' => 'lead');

    $others = array();
    foreach ($order as $sub) {
        if ($sub !== $lead && isset($qualifying[$sub])) {
            $others[$sub] = $qualifying[$sub];
        }
    }

    if (!empty($others)) {
        arsort($others);
        $sub = array_key_first($others);
        $powers[] = array('domain' => $domain, 'power' => $sub, 'tier' => $tier, 'lead' => false, 'selection' => 'secondary');
    }

    return $powers;
}

function emergence_cg_domain_pass($answers) {
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

    return array(
        'protocol_version' => 'Spark Protocol v8.5 two-pass generation',
        'answers_expected' => 28,
        'phase' => 'domain_typing',
        'scores' => $scores,
        'tiers' => $tiers,
        'manifest_threshold' => $manifest_threshold,
        'manifested' => $manifested,
        'lead_domain' => isset($manifested[0]) ? $manifested[0] : null,
        'profile_shape' => emergence_cg_profile_shape($highest_tier, $second_tier, count($manifested)),
        'provisional_spark_signature' => emergence_cg_spark_signature($highest_tier, $second_tier, 0),
        'provisional_combat_capability' => min(100, max(10, ($highest_tier * 10) + ($second_tier * 8))),
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


function emergence_cg_character_sheet($payload) {
    $lead = isset($payload['lead_domain']) ? $payload['lead_domain'] : 'Unresolved';
    $powers = isset($payload['powers']) && is_array($payload['powers']) ? $payload['powers'] : array();
    $manifested = isset($payload['manifested']) && is_array($payload['manifested']) ? $payload['manifested'] : array();
    $shape = isset($payload['profile_shape']) ? $payload['profile_shape'] : 'Unresolved profile shape.';
    $signature = isset($payload['spark_signature']) ? intval($payload['spark_signature']) : 0;
    $combat = isset($payload['combat_capability']) ? intval($payload['combat_capability']) : 0;
    $cast = isset($payload['cast']) ? $payload['cast'] : 'Unknown Cast';

    $power_names = array();
    foreach ($powers as $power) {
        if (isset($power['power'])) {
            $power_names[] = $power['power'];
        }
    }

    $title = $lead . ' Spark';
    if (count($power_names) > 0) {
        $title = $lead . ' Spark — ' . $power_names[0];
    }

    $summary = 'A ' . $cast . ' profile led by ' . $lead . '. ' . $shape;
    if (count($power_names) > 0) {
        $summary .= ' The flavor pass selected ' . implode(', ', $power_names) . '.';
    }

    return array(
        'title' => $title,
        'archetype' => $lead . ' Manifest',
        'summary' => $summary,
        'manifested_domains' => $manifested,
        'selected_powers' => $power_names,
        'signature_line' => 'Spark Signature ' . $signature . ' / Combat Capability ' . $combat,
        'battle_ready_note' => count($power_names) > 0
            ? 'This sheet is ready to become battle-simulator input.'
            : 'Power selection is incomplete until the flavor pass resolves.',
    );
}

function emergence_cg_final_pass($domain_answers, $flavor_answers) {
    $base = emergence_cg_domain_pass($domain_answers);
    $manifested = $base['manifested'];
    $tiers = $base['tiers'];

    $vectors = emergence_cg_score_flavor($flavor_answers, $manifested);
    $powers = array();

    foreach ($manifested as $domain) {
        if (!isset($vectors[$domain])) continue;
        $domain_powers = emergence_cg_select_powers($domain, $vectors[$domain], $tiers[$domain]);
        foreach ($domain_powers as $power) {
            $powers[] = $power;
        }
    }

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

    $base['phase'] = 'flavor_power_selection';
    $base['flavor_vectors'] = $vectors;
    $base['powers'] = $powers;
    $base['power_selection_status'] = 'selected_from_manifested_domains';
    $base['spark_signature'] = emergence_cg_spark_signature($highest_tier, $second_tier, count($powers));
    $base['combat_capability'] = min(100, max(10, ($highest_tier * 10) + ($second_tier * 8) + (count($powers) * 3)));
    $base['next_phase'] = array(
        'name' => 'battle_simulator',
        'description' => 'Use selected powers as the input sheet for battle simulation.',
    );
    $base['character_sheet'] = emergence_cg_character_sheet($base);

    return $base;
}

function emergence_cg_generate($domain_answers, $flavor_answers = null) {
    if (is_array($flavor_answers) && count($flavor_answers) > 0) {
        return emergence_cg_final_pass($domain_answers, $flavor_answers);
    }
    return emergence_cg_domain_pass($domain_answers);
}

function emergence_cg_domain_option_label($q, $letter) {
    $bank = emergence_cg_question_bank();
    if (!isset($bank['domain_questions']) || !is_array($bank['domain_questions'])) {
        return $letter;
    }

    foreach ($bank['domain_questions'] as $item) {
        if (intval($item['q']) === intval($q) && isset($item['options'][$letter])) {
            return $letter . '. ' . $item['options'][$letter];
        }
    }

    return $letter;
}

function emergence_cg_shortcode() {
    $bank = emergence_cg_question_bank();
    $questions = isset($bank['domain_questions']) && is_array($bank['domain_questions']) ? $bank['domain_questions'] : array();
    $letters = array('A','B','C','D','E','F','G','H');

    ob_start();
    ?>
    <section class="emergence-cg">
        <div class="ecg-hero">
            <p class="ecg-kicker">Spark Protocol v8.5 public demo</p>
            <h1>Run your Spark Type Scan</h1>
            <p class="ecg-thesis">The Emergence began as a machine for answering “Who would win?” and evolved into a world where the answer could include you.</p>
            <div class="ecg-trust-row">
                <span>Protocol v8.5 questions loaded</span>
                <span>Deterministic scoring hidden underneath</span>
                <span>28-question Spark Type Scan</span>
                <span>Manifested-domain flavor pass</span>
            </div>
        </div>

        <div class="ecg-explainer">
            <h2>Two-pass generation</h2>
            <p><strong>Pass 1:</strong> Answer the real Protocol v8.5 domain questions. The labels are psychological choices, not visible trait keys.</p>
            <p><strong>Pass 2:</strong> Only manifested domains unlock their Protocol v8.5 flavor questions.</p>
        </div>

        <form id="emergence-cg-form" class="ecg-form">
            <div class="ecg-progress">
                <span id="ecg-progress-label">0 / 28 answered</span>
                <div class="ecg-progress-bar"><span id="ecg-progress-fill"></span></div>
            </div>

            <?php foreach ($questions as $item) : ?>
                <?php $i = intval($item['q']); ?>
                <fieldset class="ecg-question">
                    <legend><?php echo esc_html('Q' . $i . ' — ' . $item['question']); ?></legend>
                    <select name="q<?php echo esc_attr($i); ?>" required>
                        <option value="">Choose one...</option>
                        <?php foreach ($letters as $letter) : ?>
                            <?php if (isset($item['options'][$letter])) : ?>
                                <option value="<?php echo esc_attr($letter); ?>">
                                    <?php echo esc_html($letter . '. ' . $item['options'][$letter]); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </fieldset>
            <?php endforeach; ?>

            <button type="submit">Run Spark Type Scan</button>
        </form>

        <div id="emergence-cg-result" class="ecg-result" aria-live="polite">
            <p class="ecg-empty">Your Spark type scan will appear here.</p>
        </div>

        <div id="emergence-cg-flavor" class="ecg-flavor" data-phase="locked">
            <p class="ecg-empty">Pass 2 unlocks here after your Spark Type Scan.</p>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('emergence_character_generator', 'emergence_cg_shortcode');

function emergence_cg_register_assets() {
    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.5.8');
    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.5.8', true);

    wp_localize_script('emergence-cg-script', 'EmergenceCG', array(
        'endpoint' => esc_url_raw(rest_url('emergence/v1/generate')),
        'nonce' => wp_create_nonce('wp_rest'),
        'question_bank' => emergence_cg_question_bank(),
    ));
}

add_action('wp_enqueue_scripts', 'emergence_cg_register_assets');

function emergence_cg_enqueue_when_shortcode($posts) {
    if (empty($posts)) return $posts;

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

    $clean_domain = array();
    foreach ($answers as $value) {
        $clean_domain[] = strtoupper(sanitize_text_field($value));
    }

    $flavor_answers = $request->get_param('flavor_answers');
    $clean_flavor = array();

    if (is_array($flavor_answers)) {
        foreach ($flavor_answers as $q => $value) {
            $clean_flavor[intval($q)] = strtoupper(sanitize_text_field($value));
        }
    }

    return rest_ensure_response(emergence_cg_generate($clean_domain, $clean_flavor));
}

add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/generate', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_rest_generate',
        'permission_callback' => '__return_true',
    ));
});


/**
 * Premium hero image provider scaffold.
 *
 * This intentionally returns a prompt-only fallback when no provider/key is
 * configured. No API key is ever returned to the browser.
 */
function emergence_cg_image_provider_config() {
    $provider = getenv('EMERGENCE_IMAGE_PROVIDER');
    if (!$provider) {
        $provider = 'disabled';
    }

    $key = getenv('EMERGENCE_IMAGE_API_KEY');
    if (!$key) {
        $key = getenv('OPENAI_API_KEY');
    }

    return array(
        'provider' => sanitize_text_field($provider),
        'configured' => !empty($key),
    );
}

function emergence_cg_sanitize_image_prompt($prompt) {
    $prompt = wp_strip_all_tags((string) $prompt);
    $prompt = preg_replace('/\s+/', ' ', $prompt);
    $prompt = trim($prompt);
    return mb_substr($prompt, 0, 3500);
}

function emergence_cg_premium_portrait_rest($request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    $spark_name = isset($params['spark_name']) ? sanitize_text_field($params['spark_name']) : '';
    $prompt = isset($params['premium_portrait_prompt']) ? emergence_cg_sanitize_image_prompt($params['premium_portrait_prompt']) : '';

    if (strlen($spark_name) < 2) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'code' => 'missing_spark_name',
            'message' => 'A named Spark is required before premium portrait generation.',
        ), 400);
    }

    if (strlen($prompt) < 80) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'code' => 'missing_prompt',
            'message' => 'A premium portrait prompt is required.',
        ), 400);
    }

    $config = emergence_cg_image_provider_config();

    if (!$config['configured'] || $config['provider'] === 'disabled') {
        return new WP_REST_Response(array(
            'status' => 'disabled',
            'provider' => $config['provider'],
            'prompt_only' => true,
            'image_url' => null,
            'spark_name' => $spark_name,
            'message' => 'Premium image provider is disabled. SVG fallback remains active and the compiled prompt is ready for manual or future provider use.',
            'premium_portrait_prompt' => $prompt,
        ), 200);
    }

    /*
     * Provider abstraction point.
     *
     * Future lane:
     * - provider=openai
     * - call image generation API server-side only
     * - store returned URL/path
     * - return public image URL
     *
     * Current lane deliberately does NOT call external APIs.
     */
    return new WP_REST_Response(array(
        'status' => 'provider_configured_not_called',
        'provider' => $config['provider'],
        'prompt_only' => true,
        'image_url' => null,
        'spark_name' => $spark_name,
        'message' => 'Provider environment is configured, but live image calls are not enabled in this scaffold lane.',
        'premium_portrait_prompt' => $prompt,
    ), 200);
}

add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/portrait', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_premium_portrait_rest',
        'permission_callback' => '__return_true',
    ));
});
