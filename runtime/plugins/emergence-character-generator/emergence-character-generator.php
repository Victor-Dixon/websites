<?php
/**
 * Plugin Name: Emergence Character Generator
 * Description: Public Spark Protocol v8.5 two-pass character generator for The Emergence.
 * Version: 0.7.5-visible-dossier-state-001
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
    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.5-visible-dossier-state-001');
    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.5-visible-dossier-state-001', true);

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
function emergence_cg_legacy_image_provider_config_disabled() {
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

function emergence_cg_legacy_sanitize_image_prompt_disabled($prompt) {
    $prompt = wp_strip_all_tags((string) $prompt);
    $prompt = preg_replace('/\s+/', ' ', $prompt);
    $prompt = trim($prompt);
    return mb_substr($prompt, 0, 3500);
}

function emergence_cg_legacy_premium_portrait_rest_disabled($request) {
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
        'callback' => 'emergence_cg_openai_premium_portrait_rest_v2',
        'permission_callback' => '__return_true',
    ));
});

// DREAMOS_OPENAI_IMAGE_PROVIDER_V2_BEGIN
/**
 * Dream.OS premium hero image provider v2.
 *
 * Server-side only. Live generation requires:
 * - EMERGENCE_IMAGE_PROVIDER=openai
 * - EMERGENCE_IMAGE_LIVE=1
 * - EMERGENCE_IMAGE_API_KEY or OPENAI_API_KEY
 */
function emergence_cg_v2_env_value($name, $default = '') {
    if (defined($name) && constant($name) !== '') {
        return constant($name);
    }

    $value = getenv($name);
    if ($value === false || $value === '') {
        return $default;
    }
    return $value;
}

function emergence_cg_v2_image_provider_config() {
    $provider = sanitize_text_field(emergence_cg_v2_env_value('EMERGENCE_IMAGE_PROVIDER', 'disabled'));
    $key = emergence_cg_v2_env_value('EMERGENCE_IMAGE_API_KEY', '');
    if (!$key) {
        $key = emergence_cg_v2_env_value('OPENAI_API_KEY', '');
    }

    $live = emergence_cg_v2_env_value('EMERGENCE_IMAGE_LIVE', '0');

    return array(
        'provider' => $provider,
        'configured' => !empty($key),
        'key' => $key,
        'live' => in_array(strtolower((string) $live), array('1', 'true', 'yes', 'on'), true),
        'model' => sanitize_text_field(emergence_cg_v2_env_value('EMERGENCE_IMAGE_MODEL', 'gpt-image-1')),
        'size' => sanitize_text_field(emergence_cg_v2_env_value('EMERGENCE_IMAGE_SIZE', '1024x1024')),
        'quality' => sanitize_text_field(emergence_cg_v2_env_value('EMERGENCE_IMAGE_QUALITY', 'medium')),
    );
}

function emergence_cg_v2_sanitize_image_prompt($prompt) {
    $prompt = wp_strip_all_tags((string) $prompt);
    $prompt = preg_replace('/\s+/', ' ', $prompt);
    $prompt = trim($prompt);
    return substr($prompt, 0, 3500);
}

function emergence_cg_v2_portrait_upload_target($spark_name, $prompt) {
    $uploads = wp_upload_dir();

    if (!empty($uploads['error'])) {
        return new WP_Error('upload_dir_error', $uploads['error']);
    }

    $dir = trailingslashit($uploads['basedir']) . 'emergence-portraits';
    $url = trailingslashit($uploads['baseurl']) . 'emergence-portraits';

    if (!wp_mkdir_p($dir)) {
        return new WP_Error('upload_dir_create_failed', 'Could not create portrait upload directory.');
    }

    $hash = substr(hash('sha256', $spark_name . '|' . $prompt), 0, 24);

    return array(
        'path' => trailingslashit($dir) . $hash . '.png',
        'url' => trailingslashit($url) . $hash . '.png',
        'hash' => $hash,
    );
}

function emergence_cg_v2_call_openai_image_provider($config, $spark_name, $prompt) {
    $target = emergence_cg_v2_portrait_upload_target($spark_name, $prompt);

    if (is_wp_error($target)) {
        return $target;
    }

    if (file_exists($target['path']) && filesize($target['path']) > 1000) {
        return array(
            'image_url' => $target['url'],
            'image_hash' => $target['hash'],
            'cached' => true,
        );
    }

    $body = array(
        'model' => $config['model'],
        'prompt' => $prompt,
        'size' => $config['size'],
        'quality' => $config['quality'],
        'n' => 1,
    );

    $response = wp_remote_post('https://api.openai.com/v1/images/generations', array(
        'timeout' => 120,
        'headers' => array(
            'Authorization' => 'Bearer ' . $config['key'],
            'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode($body),
    ));

    if (is_wp_error($response)) {
        return $response;
    }

    $code = wp_remote_retrieve_response_code($response);
    $raw = wp_remote_retrieve_body($response);
    $json = json_decode($raw, true);

    if ($code < 200 || $code >= 300) {
        $message = 'OpenAI image provider returned HTTP ' . $code . '.';
        if (is_array($json) && isset($json['error']['message'])) {
            $message .= ' ' . sanitize_text_field($json['error']['message']);
        }
        return new WP_Error('openai_image_error', $message);
    }

    if (!is_array($json) || empty($json['data'][0]['b64_json'])) {
        return new WP_Error('openai_image_missing_data', 'OpenAI image provider did not return image data.');
    }

    $binary = base64_decode($json['data'][0]['b64_json'], true);
    if (!$binary || strlen($binary) < 1000) {
        return new WP_Error('openai_image_decode_failed', 'OpenAI image payload could not be decoded.');
    }

    $written = file_put_contents($target['path'], $binary);
    if (!$written) {
        return new WP_Error('openai_image_write_failed', 'Generated portrait could not be written.');
    }

    return array(
        'image_url' => $target['url'],
        'image_hash' => $target['hash'],
        'cached' => false,
    );
}

function emergence_cg_openai_premium_portrait_rest_v2($request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    $spark_name = isset($params['spark_name']) ? sanitize_text_field($params['spark_name']) : '';
    $prompt = isset($params['premium_portrait_prompt']) ? emergence_cg_v2_sanitize_image_prompt($params['premium_portrait_prompt']) : '';

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

    $config = emergence_cg_v2_image_provider_config();

    if ($config['provider'] !== 'openai' || !$config['configured'] || !$config['live']) {
        return new WP_REST_Response(array(
            'status' => 'disabled',
            'provider' => $config['provider'],
            'live_enabled' => $config['live'],
            'prompt_only' => true,
            'image_url' => null,
            'spark_name' => $spark_name,
            'message' => 'Premium image provider is not live. SVG fallback remains active and the compiled prompt is ready.',
            'premium_portrait_prompt' => $prompt,
        ), 200);
    }

    $result = emergence_cg_v2_call_openai_image_provider($config, $spark_name, $prompt);

    if (is_wp_error($result)) {
        return new WP_REST_Response(array(
            'status' => 'provider_error',
            'provider' => 'openai',
            'prompt_only' => true,
            'image_url' => null,
            'spark_name' => $spark_name,
            'message' => $result->get_error_message(),
            'premium_portrait_prompt' => $prompt,
        ), 200);
    }

    return new WP_REST_Response(array(
        'status' => 'generated',
        'provider' => 'openai',
        'prompt_only' => false,
        'image_url' => esc_url_raw($result['image_url']),
        'image_hash' => sanitize_text_field($result['image_hash']),
        'cached' => !empty($result['cached']),
        'spark_name' => $spark_name,
        'message' => 'Premium hero portrait generated.',
    ), 200);
}

add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/portrait', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_openai_premium_portrait_rest_v2',
        'permission_callback' => '__return_true',
    ));
});
// DREAMOS_OPENAI_IMAGE_PROVIDER_V2_END

// dreamos-cg-handoff-public-asset-guard lane 098d
add_action('wp_enqueue_scripts', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }

    $base = plugin_dir_url(__FILE__) . 'assets/';
    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.5-visible-dossier-state-001');
    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.5-visible-dossier-state-001', true);
});

// DREAMOS_CHARACTER_BATTLE_HANDOFF_INLINE_BEGIN lane 098e
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-cg-battle-handoff-inline">
    (function () {
      'use strict';

      const STORAGE_KEY = 'emergence_spark_battle_handoff_v1';
      const FORBIDDEN = [
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'provisional_spark_signature',
        'provisional_combat_capability',
        'debug',
        'showwork',
        'roll',
        'odds'
      ];

      function text(sel, root) {
        const node = (root || document).querySelector(sel);
        return node ? node.textContent.trim() : '';
      }

      function listText(sel, root) {
        return Array.from((root || document).querySelectorAll(sel))
          .map(function (node) { return node.textContent.trim(); })
          .filter(Boolean);
      }

      async function saveCharacterRecord(payload, visibility) {
        const copy = Object.assign({}, payload, {visibility: visibility || 'private'});
        const response = await fetch('/wp-json/emergence/v1/characters', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({character: copy})
        });

        const data = await response.json();
        if (!response.ok || data.status !== 'saved') {
          throw new Error(data.message || 'Character save failed.');
        }

        return data;
      }

      async function createShareableSparkToken(payload) {
        const response = await fetch('/wp-json/emergence/v1/spark-token', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({spark: payload})
        });

        const data = await response.json();
        if (!response.ok || data.status !== 'created') {
          throw new Error(data.message || 'Token creation failed.');
        }

        return data;
      }

      function safePayloadFromDossier() {
        const resultRoot =
          document.querySelector('.ecg-profile-card') ||
          document.querySelector('.ecg-profile-panel') ||
          document.querySelector('[data-render="deterministic-svg"]') ||
          document.body;

        const title =
          text('.ecg-profile-card h1', resultRoot) ||
          text('.ecg-profile-card h2', resultRoot) ||
          text('h1', resultRoot) ||
          'Unnamed Spark';

        const archetype =
          text('.ecg-profile-card h3', resultRoot) ||
          text('strong', resultRoot) ||
          'Spark Profile';

        const abilities = listText('li', resultRoot).slice(0, 12).map(function (ability, index) {
          return {
            power: ability,
            domain: '',
            lead: index === 0
          };
        });

        const payload = {
          version: 1,
          source: 'emergence-character-generator',
          created_at: new Date().toISOString(),
          spark_name: title,
          title: title,
          archetype: archetype,
          summary: text('p', resultRoot),
          cast: '',
          profile_shape: '',
          selected_powers: abilities,
          battle_ready_note: 'Player-safe Spark dossier exported for battle simulation.'
        };

        const serialized = JSON.stringify(payload);
        FORBIDDEN.forEach(function (key) {
          if (serialized.indexOf(key) !== -1) {
            throw new Error('Unsafe handoff payload contains hidden key: ' + key);
          }
        });

        return payload;
      }

      function ensureButton() {
        const anchor =
          document.querySelector('.ecg-premium-prompt-panel') ||
          document.querySelector('.ecg-profile-card') ||
          document.querySelector('[data-render="deterministic-svg"]');

        if (!anchor || document.getElementById('ecg-export-to-battle-inline')) {
          return;
        }

        const panel = document.createElement('section');
        panel.className = 'ecg-profile-panel ecg-battle-handoff-panel';
        panel.innerHTML = [
          '<p class="ecg-kicker">Battle Ready</p>',
          '<h3>Use this Spark in Battle Simulator</h3>',
          '<p>Export a player-safe dossier into the battle simulator. Backend scoring stays hidden.</p>',
          '<button type="button" id="ecg-export-to-battle-inline">Use this Spark in Battle Simulator</button>',
          '<button type="button" id="ecg-save-character-record-inline">Save Character Record</button>',
          '<p id="ecg-battle-handoff-status-inline" aria-live="polite"></p>'
        ].join('');

        anchor.insertAdjacentElement('afterend', panel);
      }

      document.addEventListener('click', function (event) {
        if (!event.target || event.target.id !== 'ecg-save-character-record-inline') {
          return;
        }

        const status = document.getElementById('ecg-battle-handoff-status-inline');
        try {
          const payload = safePayloadFromDossier();
          if (status) {
            status.textContent = 'Saving character record...';
          }

          saveCharacterRecord(payload, 'private').then(function (recordData) {
            if (status) {
              status.innerHTML = 'Saved: <a href="' + recordData.reload_url + '">Reload Character</a> · <a href="' + recordData.battle_url + '">Open in Battle Simulator</a>';
            }
          }).catch(function () {
            if (status) {
              status.textContent = 'Save failed.';
            }
          });
        } catch (error) {
          if (status) {
            status.textContent = 'Save blocked: unsafe payload.';
          }
        }
      });

      document.addEventListener('click', function (event) {
        if (!event.target || event.target.id !== 'ecg-export-to-battle-inline') {
          return;
        }

        const status = document.getElementById('ecg-battle-handoff-status-inline');
        try {
          const payload = safePayloadFromDossier();
          window.localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
          if (status) {
            status.textContent = 'Creating shareable battle link...';
          }

          createShareableSparkToken(payload).then(function (tokenData) {
            if (status) {
              status.innerHTML = 'Share link ready: <a href="' + tokenData.share_url + '">Open Battle Link</a>';
            }
            window.location.href = tokenData.share_url;
          }).catch(function () {
            if (status) {
              status.textContent = 'Token unavailable. Opening same-browser Battle Simulator...';
            }
            window.location.href = '/battles/?spark_handoff=1';
          });
        } catch (error) {
          if (status) {
            status.textContent = 'Export blocked: unsafe payload.';
          }
        }
      });

      setInterval(ensureButton, 1000);
      document.addEventListener('DOMContentLoaded', ensureButton);
      ensureButton();
    })();
    </script>
    <style id="dreamos-cg-battle-handoff-inline-style">
      .ecg-battle-handoff-panel {
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 20px;
        padding: 1rem;
        margin-top: 1rem;
        background: rgba(255,255,255,.055);
      }
      .ecg-battle-handoff-panel button {
        border: 0;
        border-radius: 999px;
        padding: .85rem 1.15rem;
        font-weight: 900;
        cursor: pointer;
      }
    </style>
    <?php
});
// DREAMOS_CHARACTER_BATTLE_HANDOFF_INLINE_END

// DREAMOS_SHAREABLE_SPARK_TOKEN_REST_BEGIN lane 101
add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/spark-token', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_create_spark_token_rest',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('emergence/v1', '/spark-token/(?P<token>[A-Za-z0-9_-]{16,80})', array(
        'methods' => 'GET',
        'callback' => 'emergence_cg_read_spark_token_rest',
        'permission_callback' => '__return_true',
    ));
});

function emergence_cg_token_forbidden_keys() {
    return array(
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'provisional_spark_signature',
        'provisional_combat_capability',
        'debug',
        'showwork',
        'roll',
        'odds',
        'raw'
    );
}

function emergence_cg_sanitize_spark_token_payload($payload) {
    if (!is_array($payload)) {
        return new WP_Error('invalid_payload', 'Spark payload must be an object.');
    }

    $serialized = wp_json_encode($payload);
    foreach (emergence_cg_token_forbidden_keys() as $key) {
        if (strpos($serialized, $key) !== false) {
            return new WP_Error('unsafe_payload', 'Unsafe Spark payload blocked.');
        }
    }

    $safe = array(
        'version' => 1,
        'source' => 'emergence-character-generator',
        'spark_name' => isset($payload['spark_name']) ? sanitize_text_field($payload['spark_name']) : 'Unnamed Spark',
        'title' => isset($payload['title']) ? sanitize_text_field($payload['title']) : 'Unnamed Spark',
        'archetype' => isset($payload['archetype']) ? sanitize_text_field($payload['archetype']) : '',
        'summary' => isset($payload['summary']) ? sanitize_textarea_field($payload['summary']) : '',
        'cast' => isset($payload['cast']) ? sanitize_text_field($payload['cast']) : '',
        'profile_shape' => isset($payload['profile_shape']) ? sanitize_text_field($payload['profile_shape']) : '',
        'selected_powers' => array(),
        'battle_ready_note' => isset($payload['battle_ready_note']) ? sanitize_text_field($payload['battle_ready_note']) : 'Player-safe Spark dossier exported for battle simulation.',
    );

    if (isset($payload['selected_powers']) && is_array($payload['selected_powers'])) {
        foreach ($payload['selected_powers'] as $power) {
            if (!is_array($power)) {
                continue;
            }

            $label = isset($power['power']) ? sanitize_text_field($power['power']) : '';
            if (!$label) {
                continue;
            }

            $safe['selected_powers'][] = array(
                'power' => $label,
                'domain' => '',
                'lead' => !empty($power['lead']),
            );
        }
    }

    return $safe;
}

function emergence_cg_spark_token_secret() {
    if (defined('AUTH_SALT') && AUTH_SALT) {
        return AUTH_SALT;
    }

    if (defined('SECURE_AUTH_SALT') && SECURE_AUTH_SALT) {
        return SECURE_AUTH_SALT;
    }

    return wp_salt('auth');
}

function emergence_cg_sign_spark_token($token, $payload_json) {
    return hash_hmac('sha256', $token . '|' . $payload_json, emergence_cg_spark_token_secret());
}

function emergence_cg_create_spark_token_rest($request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    $payload = isset($params['spark']) ? $params['spark'] : $params;
    $safe = emergence_cg_sanitize_spark_token_payload($payload);

    if (is_wp_error($safe)) {
        return new WP_REST_Response(array(
            'status' => 'blocked',
            'message' => $safe->get_error_message(),
        ), 400);
    }

    $payload_json = wp_json_encode($safe);
    $token = substr(strtr(base64_encode(random_bytes(24)), '+/', '-_'), 0, 32);
    $signature = emergence_cg_sign_spark_token($token, $payload_json);

    $record = array(
        'payload' => $safe,
        'signature' => $signature,
        'created_at' => time(),
        'expires_at' => time() + (7 * DAY_IN_SECONDS),
    );

    set_transient('emergence_spark_token_' . $token, $record, 7 * DAY_IN_SECONDS);

    return new WP_REST_Response(array(
        'status' => 'created',
        'token' => $token,
        'share_url' => home_url('/battles/?spark_token=' . rawurlencode($token)),
        'expires_in_seconds' => 7 * DAY_IN_SECONDS,
        'player_safe' => true,
    ), 200);
}

function emergence_cg_read_spark_token_rest($request) {
    $token = sanitize_text_field($request['token']);

    if (!$token || !preg_match('/^[A-Za-z0-9_-]{16,80}$/', $token)) {
        return new WP_REST_Response(array(
            'status' => 'invalid',
            'message' => 'Invalid Spark token.',
        ), 404);
    }

    $record = get_transient('emergence_spark_token_' . $token);
    if (!is_array($record) || empty($record['payload']) || empty($record['signature'])) {
        return new WP_REST_Response(array(
            'status' => 'invalid',
            'message' => 'Spark token not found or expired.',
        ), 404);
    }

    if (!empty($record['expires_at']) && time() > (int) $record['expires_at']) {
        delete_transient('emergence_spark_token_' . $token);
        return new WP_REST_Response(array(
            'status' => 'expired',
            'message' => 'Spark token expired.',
        ), 404);
    }

    $payload_json = wp_json_encode($record['payload']);
    $expected = emergence_cg_sign_spark_token($token, $payload_json);

    if (!hash_equals($expected, $record['signature'])) {
        return new WP_REST_Response(array(
            'status' => 'invalid',
            'message' => 'Spark token signature rejected.',
        ), 403);
    }

    return new WP_REST_Response(array(
        'status' => 'loaded',
        'spark' => $record['payload'],
        'player_safe' => true,
    ), 200);
}
// DREAMOS_SHAREABLE_SPARK_TOKEN_REST_END

// DREAMOS_SAVED_CHARACTER_RECORDS_BEGIN lane 103
add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/characters', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_save_character_record_rest',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('emergence/v1', '/characters/(?P<record>[A-Za-z0-9_-]{12,80})', array(
        'methods' => 'GET',
        'callback' => 'emergence_cg_load_character_record_rest',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('emergence/v1', '/characters/(?P<record>[A-Za-z0-9_-]{12,80})/battle-token', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_character_record_battle_token_rest',
        'permission_callback' => '__return_true',
    ));
});

function emergence_cg_character_record_forbidden_keys() {
    return array(
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'provisional_spark_signature',
        'provisional_combat_capability',
        'debug',
        'showwork',
        'roll',
        'odds',
        'raw'
    );
}

function emergence_cg_character_record_secret() {
    if (defined('AUTH_SALT') && AUTH_SALT) {
        return AUTH_SALT;
    }

    if (defined('SECURE_AUTH_SALT') && SECURE_AUTH_SALT) {
        return SECURE_AUTH_SALT;
    }

    return wp_salt('auth');
}

function emergence_cg_character_record_sign($record_id, $payload_json) {
    return hash_hmac('sha256', $record_id . '|' . $payload_json, emergence_cg_character_record_secret());
}

function emergence_cg_sanitize_character_record_payload($payload) {
    if (!is_array($payload)) {
        return new WP_Error('invalid_payload', 'Character record payload must be an object.');
    }

    $serialized = wp_json_encode($payload);
    foreach (emergence_cg_character_record_forbidden_keys() as $key) {
        if (strpos($serialized, $key) !== false) {
            return new WP_Error('unsafe_payload', 'Unsafe character record payload blocked.');
        }
    }

    $visibility = isset($payload['visibility']) ? sanitize_text_field($payload['visibility']) : 'private';
    if (!in_array($visibility, array('public', 'private'), true)) {
        $visibility = 'private';
    }

    $safe = array(
        'version' => 1,
        'source' => 'emergence-character-generator',
        'visibility' => $visibility,
        'spark_name' => isset($payload['spark_name']) ? sanitize_text_field($payload['spark_name']) : 'Unnamed Spark',
        'title' => isset($payload['title']) ? sanitize_text_field($payload['title']) : 'Unnamed Spark',
        'archetype' => isset($payload['archetype']) ? sanitize_text_field($payload['archetype']) : '',
        'summary' => isset($payload['summary']) ? sanitize_textarea_field($payload['summary']) : '',
        'cast' => isset($payload['cast']) ? sanitize_text_field($payload['cast']) : '',
        'profile_shape' => isset($payload['profile_shape']) ? sanitize_text_field($payload['profile_shape']) : '',
        'selected_powers' => array(),
        'battle_ready_note' => isset($payload['battle_ready_note']) ? sanitize_text_field($payload['battle_ready_note']) : 'Player-safe Spark dossier saved for battle simulation.',
        'created_at' => time(),
    );

    if (isset($payload['selected_powers']) && is_array($payload['selected_powers'])) {
        foreach ($payload['selected_powers'] as $power) {
            if (!is_array($power)) {
                continue;
            }

            $label = isset($power['power']) ? sanitize_text_field($power['power']) : '';
            if (!$label) {
                continue;
            }

            $safe['selected_powers'][] = array(
                'power' => $label,
                'domain' => '',
                'lead' => !empty($power['lead']),
            );
        }
    }

    return $safe;
}

function emergence_cg_create_character_record_id() {
    return substr(strtr(base64_encode(random_bytes(18)), '+/', '-_'), 0, 24);
}

function emergence_cg_save_character_record_rest($request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    $payload = isset($params['character']) ? $params['character'] : $params;
    $safe = emergence_cg_sanitize_character_record_payload($payload);

    if (is_wp_error($safe)) {
        return new WP_REST_Response(array(
            'status' => 'blocked',
            'message' => $safe->get_error_message(),
        ), 400);
    }

    $record_id = emergence_cg_create_character_record_id();
    $payload_json = wp_json_encode($safe);
    $signature = emergence_cg_character_record_sign($record_id, $payload_json);

    $record = array(
        'payload' => $safe,
        'signature' => $signature,
        'created_at' => time(),
        'expires_at' => time() + (30 * DAY_IN_SECONDS),
    );

    set_transient('emergence_character_record_' . $record_id, $record, 30 * DAY_IN_SECONDS);

    return new WP_REST_Response(array(
        'status' => 'saved',
        'record_id' => $record_id,
        'visibility' => $safe['visibility'],
        'reload_url' => home_url('/character-generator/?character_record=' . rawurlencode($record_id)),
        'battle_url' => home_url('/battles/?character_record=' . rawurlencode($record_id)),
        'expires_in_seconds' => 30 * DAY_IN_SECONDS,
        'player_safe' => true,
    ), 200);
}

function emergence_cg_get_character_record($record_id) {
    $record_id = sanitize_text_field($record_id);

    if (!$record_id || !preg_match('/^[A-Za-z0-9_-]{12,80}$/', $record_id)) {
        return new WP_Error('invalid_record', 'Invalid character record.');
    }

    $record = get_transient('emergence_character_record_' . $record_id);
    if (!is_array($record) || empty($record['payload']) || empty($record['signature'])) {
        return new WP_Error('not_found', 'Character record not found or expired.');
    }

    if (!empty($record['expires_at']) && time() > (int) $record['expires_at']) {
        delete_transient('emergence_character_record_' . $record_id);
        return new WP_Error('expired', 'Character record expired.');
    }

    $payload_json = wp_json_encode($record['payload']);
    $expected = emergence_cg_character_record_sign($record_id, $payload_json);

    if (!hash_equals($expected, $record['signature'])) {
        return new WP_Error('signature_rejected', 'Character record signature rejected.');
    }

    return $record;
}

function emergence_cg_load_character_record_rest($request) {
    $record_id = sanitize_text_field($request['record']);
    $record = emergence_cg_get_character_record($record_id);

    if (is_wp_error($record)) {
        return new WP_REST_Response(array(
            'status' => 'invalid',
            'message' => $record->get_error_message(),
        ), 404);
    }

    return new WP_REST_Response(array(
        'status' => 'loaded',
        'record_id' => $record_id,
        'character' => $record['payload'],
        'player_safe' => true,
    ), 200);
}

function emergence_cg_character_record_battle_token_rest($request) {
    $record_id = sanitize_text_field($request['record']);
    $record = emergence_cg_get_character_record($record_id);

    if (is_wp_error($record)) {
        return new WP_REST_Response(array(
            'status' => 'invalid',
            'message' => $record->get_error_message(),
        ), 404);
    }

    $safe = $record['payload'];
    $payload_json = wp_json_encode($safe);
    $token = substr(strtr(base64_encode(random_bytes(24)), '+/', '-_'), 0, 32);

    if (function_exists('emergence_cg_sign_spark_token')) {
        $signature = emergence_cg_sign_spark_token($token, $payload_json);
    } else {
        $signature = hash_hmac('sha256', $token . '|' . $payload_json, emergence_cg_character_record_secret());
    }

    set_transient('emergence_spark_token_' . $token, array(
        'payload' => $safe,
        'signature' => $signature,
        'created_at' => time(),
        'expires_at' => time() + (7 * DAY_IN_SECONDS),
    ), 7 * DAY_IN_SECONDS);

    return new WP_REST_Response(array(
        'status' => 'created',
        'token' => $token,
        'share_url' => home_url('/battles/?spark_token=' . rawurlencode($token)),
        'record_id' => $record_id,
        'player_safe' => true,
    ), 200);
}
// DREAMOS_SAVED_CHARACTER_RECORDS_END

// DREAMOS_PUBLIC_SHARE_CARD_UI_BEGIN lane 105
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-public-share-card-ui-inline">
    (function () {
      'use strict';

      const STATUS_ID = 'ecg-battle-handoff-status-inline';
      const CARD_CLASS = 'ecg-share-card';
      const FORBIDDEN = [
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'provisional_spark_signature',
        'provisional_combat_capability',
        'debug',
        'showwork',
        'roll',
        'odds',
        'raw'
      ];

      function esc(value) {
        return String(value == null ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function noHiddenKeys(value) {
        const serialized = String(value || '');
        FORBIDDEN.forEach(function (key) {
          if (serialized.indexOf(key) !== -1) {
            throw new Error('Hidden key blocked from share card: ' + key);
          }
        });
      }

      function copyText(value, button) {
        noHiddenKeys(value);

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(value).then(function () {
            button.textContent = 'Copied';
            setTimeout(function () { button.textContent = button.getAttribute('data-copy-label') || 'Copy'; }, 1400);
          }).catch(function () {
            fallbackCopy(value, button);
          });
          return;
        }

        fallbackCopy(value, button);
      }

      function fallbackCopy(value, button) {
        const area = document.createElement('textarea');
        area.value = value;
        area.setAttribute('readonly', 'readonly');
        area.style.position = 'fixed';
        area.style.left = '-9999px';
        document.body.appendChild(area);
        area.select();
        try {
          document.execCommand('copy');
          button.textContent = 'Copied';
        } catch (error) {
          button.textContent = 'Copy failed';
        }
        document.body.removeChild(area);
        setTimeout(function () { button.textContent = button.getAttribute('data-copy-label') || 'Copy'; }, 1400);
      }

      function linkRow(label, href, copyLabel) {
        noHiddenKeys(href);
        return [
          '<div class="ecg-share-card-row">',
          '<div>',
          '<span class="ecg-share-card-label">' + esc(label) + '</span>',
          '<a href="' + esc(href) + '">' + esc(href) + '</a>',
          '</div>',
          '<button type="button" class="ecg-copy-link-button" data-copy-value="' + esc(href) + '" data-copy-label="' + esc(copyLabel) + '">' + esc(copyLabel) + '</button>',
          '</div>'
        ].join('');
      }

      function renderShareCard(links) {
        return [
          '<section class="' + CARD_CLASS + '" data-share-card="saved-character">',
          '<p class="ecg-kicker">Saved Character</p>',
          '<h3>Share / Reload / Battle Links</h3>',
          '<p>Your Spark dossier was saved as a player-safe record. Use these links to reload the character or send it into battle.</p>',
          linkRow('Reload Character', links.reload, 'Copy Reload Link'),
          linkRow('Share Character', links.share, 'Copy Share Link'),
          linkRow('Open in Battle Simulator', links.battle, 'Copy Battle Link'),
          '<div class="ecg-share-card-actions">',
          '<a class="ecg-share-card-cta" href="' + esc(links.reload) + '">Reload Character</a>',
          '<a class="ecg-share-card-cta" href="' + esc(links.battle) + '">Open Battle Link</a>',
          '</div>',
          '</section>'
        ].join('');
      }

      function extractLinks(status) {
        const links = Array.from(status.querySelectorAll('a[href]')).map(function (a) {
          return a.href;
        });

        const reload = links.find(function (href) { return href.indexOf('character_record=') !== -1 && href.indexOf('/character-generator/') !== -1; }) || '';
        const battle = links.find(function (href) { return href.indexOf('character_record=') !== -1 && href.indexOf('/battles/') !== -1; }) || '';

        if (!reload || !battle) {
          return null;
        }

        return {
          reload: reload,
          share: reload,
          battle: battle
        };
      }

      function upgradeStatus() {
        const status = document.getElementById(STATUS_ID);
        if (!status || status.getAttribute('data-share-card-upgraded') === '1') {
          return;
        }

        const links = extractLinks(status);
        if (!links) {
          return;
        }

        const html = renderShareCard(links);
        noHiddenKeys(html);
        status.innerHTML = html;
        status.setAttribute('data-share-card-upgraded', '1');
      }

      document.addEventListener('click', function (event) {
        const button = event.target && event.target.closest ? event.target.closest('.ecg-copy-link-button') : null;
        if (!button) {
          return;
        }

        copyText(button.getAttribute('data-copy-value') || '', button);
      });

      const observer = new MutationObserver(upgradeStatus);
      document.addEventListener('DOMContentLoaded', function () {
        const status = document.getElementById(STATUS_ID);
        if (status) {
          observer.observe(status, {childList: true, subtree: true, characterData: true});
        }
        upgradeStatus();
      });

      setInterval(upgradeStatus, 1000);
      upgradeStatus();
    })();
    </script>
    <style id="dreamos-public-share-card-ui-style">
      .ecg-share-card {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 22px;
        border: 1px solid rgba(255,255,255,.18);
        background: linear-gradient(135deg, rgba(255,255,255,.09), rgba(255,255,255,.035));
      }

      .ecg-share-card h3 {
        margin-top: .25rem;
      }

      .ecg-share-card-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .75rem;
        align-items: center;
        margin: .75rem 0;
        padding: .75rem;
        border-radius: 16px;
        background: rgba(0,0,0,.16);
      }

      .ecg-share-card-label {
        display: block;
        font-size: .78rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        opacity: .75;
        margin-bottom: .2rem;
      }

      .ecg-share-card-row a {
        word-break: break-word;
      }

      .ecg-copy-link-button,
      .ecg-share-card-cta {
        border: 0;
        border-radius: 999px;
        padding: .72rem .95rem;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
      }

      .ecg-share-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        margin-top: 1rem;
      }

      @media (max-width: 680px) {
        .ecg-share-card-row {
          grid-template-columns: 1fr;
        }
      }
    </style>
    <?php
});
// DREAMOS_PUBLIC_SHARE_CARD_UI_END

// DREAMOS_PREMIUM_PORTRAIT_DESIGN_CONTROLS_BEGIN lane 107c
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-premium-portrait-design-controls-inline">
    (function () {
      'use strict';

      function ensureDesignControls() {
        var totality = document.querySelector('.ecg-totality-form, .ecg-premium-prompt-panel, .ecg-cosmetic-grid');
        if (!totality || document.getElementById('dreamos-design-control-fallback')) {
          return;
        }

        var panel = document.createElement('section');
        panel.id = 'dreamos-design-control-fallback';
        panel.className = 'ecg-cosmetic-grid ecg-design-control-fallback';
        panel.innerHTML = [
          '<label>Costume Concept<input id="emergence-costume-style" type="text" maxlength="160" placeholder="Example: armored hooded suit, sleek tactical jacket, cosmic cape, cracked gold mask"></label>',
          '<label>Personality / Attitude<input id="emergence-personality-style" type="text" maxlength="120" placeholder="Example: stoic protector, cocky street hero, haunted survivor, noble guardian"></label>',
          '<p class="ecg-result-note">FULL BODY REVEAL STANDARD: the premium prompt uses a complete head-to-toe superhero reveal as the default.</p>',
          '<p class="ecg-result-note">CUSTOM COSTUME DIRECTION and CUSTOM PERSONALITY / ATTITUDE are player-written design inputs.</p>'
        ].join('');

        totality.insertAdjacentElement('beforebegin', panel);
      }

      document.addEventListener('DOMContentLoaded', ensureDesignControls);
      setInterval(ensureDesignControls, 1000);
      ensureDesignControls();
    })();
    </script>
    <style id="dreamos-premium-portrait-design-controls-style">
      .ecg-design-control-fallback {
        margin: 1rem 0;
        padding: 1rem;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,.16);
        background: rgba(255,255,255,.055);
      }

      .ecg-design-control-fallback input {
        width: 100%;
        border-radius: 14px;
        padding: .75rem;
        margin-top: .35rem;
      }
    </style>
    <?php
});
// DREAMOS_PREMIUM_PORTRAIT_DESIGN_CONTROLS_END

// DREAMOS_PORTRAIT_PROMPT_PREVIEW_POLISH_BEGIN lane 109
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-portrait-prompt-preview-polish-inline">
    (function () {
      'use strict';

      const FORBIDDEN = [
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'provisional_spark_signature',
        'provisional_combat_capability',
        'showwork',
        'raw_roll',
        'odds:'
      ];

      function esc(value) {
        return String(value == null ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function assertSafe(text) {
        const lower = String(text || '').toLowerCase();
        FORBIDDEN.forEach(function (key) {
          if (lower.indexOf(key) !== -1) {
            throw new Error('Unsafe portrait prompt preview blocked: ' + key);
          }
        });
      }

      function readPromptText() {
        const textarea = document.querySelector('.ecg-premium-prompt');
        return textarea ? textarea.value || textarea.textContent || '' : '';
      }

      function lineAfter(prompt, prefix) {
        const lines = String(prompt || '').split(/\n+/);
        const found = lines.find(function (line) {
          return line.toLowerCase().indexOf(prefix.toLowerCase()) === 0;
        });
        return found ? found.replace(new RegExp('^' + prefix.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*', 'i'), '').trim() : '';
      }

      function includesLine(prompt, phrase) {
        return String(prompt || '').toLowerCase().indexOf(phrase.toLowerCase()) !== -1;
      }

      function extractName(prompt) {
        const match = String(prompt || '').match(/hero named ([^.]+)\./i);
        return match ? match[1].trim() : 'Your Spark';
      }

      function copyText(value, button) {
        assertSafe(value);

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(value).then(function () {
            button.textContent = 'Copied';
            setTimeout(function () { button.textContent = button.getAttribute('data-label') || 'Copy Prompt'; }, 1400);
          }).catch(function () {
            fallbackCopy(value, button);
          });
          return;
        }

        fallbackCopy(value, button);
      }

      function fallbackCopy(value, button) {
        const area = document.createElement('textarea');
        area.value = value;
        area.setAttribute('readonly', 'readonly');
        area.style.position = 'fixed';
        area.style.left = '-9999px';
        document.body.appendChild(area);
        area.select();

        try {
          document.execCommand('copy');
          button.textContent = 'Copied';
        } catch (error) {
          button.textContent = 'Copy failed';
        }

        document.body.removeChild(area);
        setTimeout(function () { button.textContent = button.getAttribute('data-label') || 'Copy Prompt'; }, 1400);
      }

      function renderSection(label, value) {
        return [
          '<div class="ecg-prompt-preview-section">',
          '<span>' + esc(label) + '</span>',
          '<p>' + esc(value || 'System-selected from your Spark profile.') + '</p>',
          '</div>'
        ].join('');
      }

      function renderPreview(prompt) {
        assertSafe(prompt);

        const name = extractName(prompt);
        const costume = lineAfter(prompt, 'CUSTOM COSTUME DIRECTION:');
        const personality = lineAfter(prompt, 'CUSTOM PERSONALITY / ATTITUDE:');
        const powers = lineAfter(prompt, 'POWERS TO VISUALLY SHOWCASE:');
        const showcase = lineAfter(prompt, 'ABILITY SHOWCASE MODE:');
        const composition = includesLine(prompt, 'FULL BODY REVEAL STANDARD')
          ? 'Full-body reveal: complete head-to-toe superhero design, full costume visible, no cropped portrait.'
          : lineAfter(prompt, 'COMPOSITION:');

        return [
          '<section class="ecg-prompt-preview-card" data-prompt-preview="polished">',
          '<div class="ecg-prompt-preview-head">',
          '<div>',
          '<p class="ecg-kicker">Premium Portrait Prompt</p>',
          '<h3>' + esc(name) + '</h3>',
          '<p>Copy-ready art direction for the premium superhero image generator.</p>',
          '</div>',
          '<button type="button" class="ecg-copy-prompt-button" data-label="Copy Prompt">Copy Prompt</button>',
          '</div>',
          '<div class="ecg-prompt-preview-grid">',
          renderSection('Costume', costume),
          renderSection('Personality', personality),
          renderSection('Powers to Showcase', powers),
          renderSection('Ability Showcase', showcase),
          renderSection('Full-Body Standard', composition),
          '</div>',
          '<details class="ecg-prompt-preview-raw">',
          '<summary>View full copy prompt</summary>',
          '<pre>' + esc(prompt) + '</pre>',
          '</details>',
          '</section>'
        ].join('');
      }

      function upgradePromptPreview() {
        const textarea = document.querySelector('.ecg-premium-prompt');
        if (!textarea || document.querySelector('[data-prompt-preview="polished"]')) {
          return;
        }

        const prompt = readPromptText();
        if (!prompt || prompt.indexOf('FULL BODY REVEAL STANDARD') === -1) {
          return;
        }

        const panel = document.createElement('div');
        panel.innerHTML = renderPreview(prompt);
        textarea.insertAdjacentElement('beforebegin', panel.firstElementChild);
        textarea.setAttribute('data-polished-preview-source', '1');
      }

      document.addEventListener('click', function (event) {
        const button = event.target && event.target.closest ? event.target.closest('.ecg-copy-prompt-button') : null;
        if (!button) {
          return;
        }

        copyText(readPromptText(), button);
      });

      document.addEventListener('DOMContentLoaded', upgradePromptPreview);
      setInterval(upgradePromptPreview, 1000);
      upgradePromptPreview();
    })();
    </script>
    <style id="dreamos-portrait-prompt-preview-polish-style">
      .ecg-prompt-preview-card {
        margin: 1rem 0;
        padding: 1rem;
        border-radius: 24px;
        border: 1px solid rgba(255,255,255,.18);
        background: linear-gradient(135deg, rgba(255,255,255,.10), rgba(255,255,255,.035));
      }

      .ecg-prompt-preview-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 1rem;
      }

      .ecg-prompt-preview-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
      }

      .ecg-prompt-preview-section {
        padding: .85rem;
        border-radius: 18px;
        background: rgba(0,0,0,.18);
      }

      .ecg-prompt-preview-section span {
        display: block;
        font-size: .76rem;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        opacity: .76;
        margin-bottom: .25rem;
      }

      .ecg-prompt-preview-section p {
        margin: 0;
      }

      .ecg-copy-prompt-button {
        border: 0;
        border-radius: 999px;
        padding: .75rem 1rem;
        font-weight: 900;
        cursor: pointer;
      }

      .ecg-prompt-preview-raw {
        margin-top: 1rem;
      }

      .ecg-prompt-preview-raw pre {
        white-space: pre-wrap;
        word-break: break-word;
        max-height: 320px;
        overflow: auto;
        padding: .85rem;
        border-radius: 16px;
        background: rgba(0,0,0,.24);
      }

      @media (max-width: 760px) {
        .ecg-prompt-preview-head {
          display: block;
        }

        .ecg-copy-prompt-button {
          margin-top: .75rem;
          width: 100%;
        }

        .ecg-prompt-preview-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
    <?php
});
// DREAMOS_PORTRAIT_PROMPT_PREVIEW_POLISH_END

// DREAMOS_SCAN_SUBMIT_STATE_RESET_GUARD_BEGIN lane 110b
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-scan-submit-state-reset-guard-inline">
    (function () {
      'use strict';

      const DRAFT_KEY = 'emergence_cg_answer_draft_v1';
      const ROOT_SELECTORS = [
        '#emergence-character-generator',
        '.emergence-character-generator',
        '.ecg-shell',
        '.ecg-app',
        '.ecg-wrap',
        '[data-emergence-character-generator]'
      ];

      const FORBIDDEN = [
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'showwork',
        'raw_roll',
        'odds:'
      ];

      function root() {
        for (let i = 0; i < ROOT_SELECTORS.length; i += 1) {
          const found = document.querySelector(ROOT_SELECTORS[i]);
          if (found) {
            return found;
          }
        }

        const shortcode = document.querySelector('[id*="emergence"], [class*="emergence"], [class*="ecg-"]');
        return shortcode || document.body;
      }

      function safeText(value) {
        const lower = String(value || '').toLowerCase();
        FORBIDDEN.forEach(function (key) {
          if (lower.indexOf(key) !== -1) {
            throw new Error('Unsafe draft payload blocked: ' + key);
          }
        });
      }

      function collectAnswers() {
        const scope = root();
        const answers = {};

        scope.querySelectorAll('input, select, textarea').forEach(function (field, index) {
          const key = field.name || field.id || field.getAttribute('data-question-id') || ('field_' + index);

          if (field.type === 'radio') {
            if (field.checked) {
              answers[key] = field.value;
            }
            return;
          }

          if (field.type === 'checkbox') {
            answers[key] = !!field.checked;
            return;
          }

          answers[key] = field.value;
        });

        return answers;
      }

      function saveDraft() {
        try {
          const payload = {
            saved_at: Date.now(),
            path: window.location.pathname,
            answers: collectAnswers()
          };

          const serialized = JSON.stringify(payload);
          safeText(serialized);
          window.sessionStorage.setItem(DRAFT_KEY, serialized);
        } catch (error) {
          console.warn('[EmergenceCG] draft save skipped');
        }
      }

      function restoreDraft() {
        try {
          const raw = window.sessionStorage.getItem(DRAFT_KEY);
          if (!raw) {
            return;
          }

          safeText(raw);
          const payload = JSON.parse(raw);
          const answers = payload && payload.answers ? payload.answers : {};
          const scope = root();

          Object.keys(answers).forEach(function (key) {
            const escaped = window.CSS && CSS.escape ? CSS.escape(key) : key.replace(/"/g, '\"');
            const fields = Array.from(scope.querySelectorAll('[name="' + escaped + '"], #' + escaped));

            fields.forEach(function (field) {
              const value = answers[key];

              if (field.type === 'radio') {
                field.checked = String(field.value) === String(value);
                return;
              }

              if (field.type === 'checkbox') {
                field.checked = !!value;
                return;
              }

              if (!field.value) {
                field.value = value;
              }
            });
          });
        } catch (error) {
          console.warn('[EmergenceCG] draft restore skipped');
        }
      }

      function isInsideGenerator(target) {
        const scope = root();
        return !!(target && scope && (target === scope || scope.contains(target)));
      }

      function hardenButtons() {
        const scope = root();

        scope.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
          button.setAttribute('type', 'button');
          button.setAttribute('data-dreamos-submit-safe', '1');
        });
      }

      function hardenForms() {
        const scope = root();

        scope.querySelectorAll('form').forEach(function (form) {
          if (form.getAttribute('data-dreamos-submit-guard') === '1') {
            return;
          }

          form.setAttribute('data-dreamos-submit-guard', '1');
          form.addEventListener('submit', function (event) {
            if (!isInsideGenerator(event.target)) {
              return;
            }

            saveDraft();
            event.preventDefault();
            event.stopPropagation();
            return false;
          }, true);
        });
      }

      document.addEventListener('input', function (event) {
        if (isInsideGenerator(event.target)) {
          saveDraft();
        }
      }, true);

      document.addEventListener('change', function (event) {
        if (isInsideGenerator(event.target)) {
          saveDraft();
        }
      }, true);

      document.addEventListener('click', function (event) {
        if (isInsideGenerator(event.target)) {
          saveDraft();
        }
      }, true);

      function boot() {
        restoreDraft();
        hardenButtons();
        hardenForms();
      }

      document.addEventListener('DOMContentLoaded', boot);
      setInterval(boot, 750);
      boot();

      window.DreamOSEmergenceScanStateGuard = {
        key: DRAFT_KEY,
        saveDraft: saveDraft,
        restoreDraft: restoreDraft,
        collectAnswers: collectAnswers,
        hardenButtons: hardenButtons,
        hardenForms: hardenForms
      };
    })();
    </script>
    <?php
});
// DREAMOS_SCAN_SUBMIT_STATE_RESET_GUARD_END

// DREAMOS_PRIVACY_SAFE_EVENT_TRACKING_BEGIN lane 111
add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/events', array(
        'methods' => 'POST',
        'callback' => 'emergence_cg_track_event_rest',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('emergence/v1', '/events/summary', array(
        'methods' => 'GET',
        'callback' => 'emergence_cg_event_summary_rest',
        'permission_callback' => '__return_true',
    ));
});

function emergence_cg_tracking_allowed_events() {
    return array(
        'character_started',
        'scan_completed',
        'flavor_completed',
        'totality_completed',
        'premium_prompt_viewed',
        'premium_prompt_copied',
        'premium_image_requested',
        'premium_image_fallback',
        'character_saved',
        'character_reloaded',
        'share_link_clicked',
        'battle_opened',
        'battle_started',
        'battle_completed',
        'battle_token_created',
        'battle_record_loaded'
    );
}

function emergence_cg_tracking_forbidden_keys() {
    return array(
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'provisional_spark_signature',
        'provisional_combat_capability',
        'answers',
        'domain_key',
        'debug',
        'showwork',
        'raw',
        'roll',
        'odds',
        'api_key',
        'token_secret'
    );
}

function emergence_cg_tracking_storage_key() {
    return 'emergence_event_counts_v1';
}

function emergence_cg_tracking_is_safe_payload($payload) {
    $serialized = strtolower(wp_json_encode($payload));
    foreach (emergence_cg_tracking_forbidden_keys() as $key) {
        if (strpos($serialized, strtolower($key)) !== false) {
            return false;
        }
    }

    return true;
}

function emergence_cg_tracking_sanitize_context($context) {
    if (!is_array($context)) {
        return array();
    }

    $safe = array();
    $allowed = array(
        'source',
        'page',
        'phase',
        'visibility',
        'result',
        'provider_status',
        'has_record',
        'has_token',
        'opponent',
        'button',
        'version'
    );

    foreach ($allowed as $key) {
        if (isset($context[$key])) {
            $value = $context[$key];

            if (is_bool($value)) {
                $safe[$key] = $value;
            } elseif (is_numeric($value)) {
                $safe[$key] = (int) $value;
            } else {
                $safe[$key] = sanitize_text_field((string) $value);
            }
        }
    }

    return $safe;
}

function emergence_cg_track_event_rest($request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    if (!emergence_cg_tracking_is_safe_payload($params)) {
        return new WP_REST_Response(array(
            'status' => 'blocked',
            'message' => 'Unsafe analytics payload blocked.',
        ), 400);
    }

    $event = isset($params['event']) ? sanitize_text_field($params['event']) : '';
    if (!in_array($event, emergence_cg_tracking_allowed_events(), true)) {
        return new WP_REST_Response(array(
            'status' => 'blocked',
            'message' => 'Unknown analytics event.',
        ), 400);
    }

    $context = emergence_cg_tracking_sanitize_context(isset($params['context']) ? $params['context'] : array());
    if (!emergence_cg_tracking_is_safe_payload($context)) {
        return new WP_REST_Response(array(
            'status' => 'blocked',
            'message' => 'Unsafe analytics context blocked.',
        ), 400);
    }

    $key = emergence_cg_tracking_storage_key();
    $counts = get_option($key, array());
    if (!is_array($counts)) {
        $counts = array();
    }

    if (!isset($counts[$event])) {
        $counts[$event] = 0;
    }

    $counts[$event] += 1;
    $counts['_total'] = isset($counts['_total']) ? ((int) $counts['_total'] + 1) : 1;
    $counts['_last_event'] = $event;
    $counts['_last_at'] = time();

    update_option($key, $counts, false);

    return new WP_REST_Response(array(
        'status' => 'tracked',
        'event' => $event,
        'player_safe' => true,
    ), 200);
}

function emergence_cg_event_summary_rest($request) {
    $counts = get_option(emergence_cg_tracking_storage_key(), array());
    if (!is_array($counts)) {
        $counts = array();
    }

    return new WP_REST_Response(array(
        'status' => 'ok',
        'summary' => $counts,
        'player_safe' => true,
    ), 200);
}
// DREAMOS_PRIVACY_SAFE_EVENT_TRACKING_END

// DREAMOS_PRIVACY_SAFE_EVENT_TRACKING_INLINE_BEGIN lane 111
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-privacy-safe-event-tracking-inline">
    (function () {
      'use strict';

      const ENDPOINT = '/wp-json/emergence/v1/events';
      const FORBIDDEN = [
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'answers',
        'debug',
        'showwork',
        'raw',
        'roll',
        'odds',
        'api_key'
      ];

      function safePayload(payload) {
        const serialized = JSON.stringify(payload || {}).toLowerCase();
        FORBIDDEN.forEach(function (key) {
          if (serialized.indexOf(key) !== -1) {
            throw new Error('Unsafe analytics payload blocked: ' + key);
          }
        });
      }

      function track(eventName, context) {
        const payload = {
          event: eventName,
          context: Object.assign({
            source: 'character-generator',
            page: window.location.pathname,
            version: '111'
          }, context || {})
        };

        try {
          safePayload(payload);
        } catch (error) {
          console.warn('[EmergenceCG] tracking blocked');
          return;
        }

        try {
          fetch(ENDPOINT, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload),
            keepalive: true
          }).catch(function () {});
        } catch (error) {}
      }

      function textOf(target) {
        return String((target && (target.textContent || target.value)) || '').toLowerCase();
      }

      document.addEventListener('DOMContentLoaded', function () {
        track('character_started', {phase: 'page_loaded'});
      });

      document.addEventListener('click', function (event) {
        const button = event.target && event.target.closest ? event.target.closest('button, a') : null;
        if (!button) {
          return;
        }

        const text = textOf(button);
        const href = button.href || '';

        if (text.indexOf('copy prompt') !== -1) {
          track('premium_prompt_copied', {button: 'copy_prompt'});
          return;
        }

        if (text.indexOf('save character') !== -1) {
          track('character_saved', {button: 'save_character'});
          return;
        }

        if (text.indexOf('battle') !== -1 || href.indexOf('/battles/') !== -1) {
          track('battle_opened', {button: 'battle_link'});
          return;
        }

        if (text.indexOf('share') !== -1 || href.indexOf('character_record=') !== -1 || href.indexOf('spark_token=') !== -1) {
          track('share_link_clicked', {button: 'share_link'});
        }
      }, true);

      window.DreamOSEmergenceTrackEvent = track;
    })();
    </script>
    <?php
});
// DREAMOS_PRIVACY_SAFE_EVENT_TRACKING_INLINE_END

// DREAMOS_ADMIN_EVENT_DASHBOARD_BEGIN lane 113
add_action('admin_menu', function () {
    add_menu_page(
        'Emergence Events',
        'Emergence Events',
        'manage_options',
        'emergence-events',
        'emergence_cg_render_admin_event_dashboard',
        'dashicons-chart-bar',
        56
    );
});

add_action('rest_api_init', function () {
    register_rest_route('emergence/v1', '/events/admin-summary', array(
        'methods' => 'GET',
        'callback' => 'emergence_cg_admin_event_summary_rest',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));
});

function emergence_cg_admin_dashboard_safe_counts() {
    $counts = get_option(emergence_cg_tracking_storage_key(), array());
    if (!is_array($counts)) {
        $counts = array();
    }

    $allowed = array(
        'character_started',
        'scan_completed',
        'flavor_completed',
        'totality_completed',
        'premium_prompt_viewed',
        'premium_prompt_copied',
        'premium_image_requested',
        'premium_image_fallback',
        'character_saved',
        'character_reloaded',
        'share_link_clicked',
        'battle_opened',
        'battle_started',
        'battle_completed',
        'battle_token_created',
        'battle_record_loaded',
        '_total',
        '_last_event',
        '_last_at'
    );

    $safe = array();
    foreach ($allowed as $key) {
        if (isset($counts[$key])) {
            if ($key === '_last_event') {
                $safe[$key] = sanitize_text_field((string) $counts[$key]);
            } else {
                $safe[$key] = (int) $counts[$key];
            }
        } else {
            $safe[$key] = ($key === '_last_event') ? '' : 0;
        }
    }

    return $safe;
}

function emergence_cg_admin_event_summary_rest($request) {
    if (!current_user_can('manage_options')) {
        return new WP_REST_Response(array(
            'status' => 'forbidden',
            'message' => 'Admin access required.',
        ), 403);
    }

    return new WP_REST_Response(array(
        'status' => 'ok',
        'summary' => emergence_cg_admin_dashboard_safe_counts(),
        'player_safe' => true,
    ), 200);
}

function emergence_cg_admin_metric_card($label, $value, $note = '') {
    ?>
    <div class="emergence-admin-metric-card">
        <span><?php echo esc_html($label); ?></span>
        <strong><?php echo esc_html((string) $value); ?></strong>
        <?php if ($note !== '') : ?>
            <p><?php echo esc_html($note); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

function emergence_cg_admin_event_rate($numerator, $denominator) {
    $numerator = max(0, (int) $numerator);
    $denominator = max(0, (int) $denominator);

    if ($denominator <= 0) {
        return '0%';
    }

    return round(($numerator / $denominator) * 100, 1) . '%';
}

function emergence_cg_render_admin_event_dashboard() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Admin access required.', 'emergence-character-generator'));
    }

    $counts = emergence_cg_admin_dashboard_safe_counts();

    $started = (int) $counts['character_started'];
    $scanned = (int) $counts['scan_completed'];
    $saved = (int) $counts['character_saved'];
    $copied = (int) $counts['premium_prompt_copied'];
    $battle_opened = (int) $counts['battle_opened'];
    $battle_started = (int) $counts['battle_started'];
    $battle_completed = (int) $counts['battle_completed'];
    ?>
    <div class="wrap emergence-admin-dashboard">
        <h1>Emergence Event Dashboard</h1>
        <p class="description">
            Privacy-safe first-party event counts. This dashboard stores counts only. It does not expose answers, raw scores, tiers, hidden routing, or backend math.
        </p>

        <div class="emergence-admin-grid">
            <?php emergence_cg_admin_metric_card('Character Started', $started, 'Visitors who loaded the generator.'); ?>
            <?php emergence_cg_admin_metric_card('Scan Completed', $scanned, 'Users who completed the first Spark scan.'); ?>
            <?php emergence_cg_admin_metric_card('Prompt Copied', $copied, 'Premium portrait prompt copy actions.'); ?>
            <?php emergence_cg_admin_metric_card('Character Saved', $saved, 'Saved character record actions.'); ?>
            <?php emergence_cg_admin_metric_card('Battle Opened', $battle_opened, 'Battle page opens from links or visits.'); ?>
            <?php emergence_cg_admin_metric_card('Battle Started', $battle_started, 'Users who started a battle.'); ?>
            <?php emergence_cg_admin_metric_card('Battle Completed', $battle_completed, 'Battle completion events, when available.'); ?>
            <?php emergence_cg_admin_metric_card('Total Events', $counts['_total'], 'All accepted safe events.'); ?>
        </div>

        <h2>Funnel Snapshot</h2>
        <table class="widefat striped emergence-admin-table">
            <thead>
                <tr>
                    <th>Step</th>
                    <th>Count</th>
                    <th>Rate from Character Started</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Scan Completed</td>
                    <td><?php echo esc_html((string) $scanned); ?></td>
                    <td><?php echo esc_html(emergence_cg_admin_event_rate($scanned, $started)); ?></td>
                </tr>
                <tr>
                    <td>Prompt Copied</td>
                    <td><?php echo esc_html((string) $copied); ?></td>
                    <td><?php echo esc_html(emergence_cg_admin_event_rate($copied, $started)); ?></td>
                </tr>
                <tr>
                    <td>Character Saved</td>
                    <td><?php echo esc_html((string) $saved); ?></td>
                    <td><?php echo esc_html(emergence_cg_admin_event_rate($saved, $started)); ?></td>
                </tr>
                <tr>
                    <td>Battle Started</td>
                    <td><?php echo esc_html((string) $battle_started); ?></td>
                    <td><?php echo esc_html(emergence_cg_admin_event_rate($battle_started, $started)); ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Last Event</h2>
        <p>
            <strong><?php echo esc_html($counts['_last_event'] ?: 'none'); ?></strong>
            <?php if (!empty($counts['_last_at'])) : ?>
                at <?php echo esc_html(date_i18n('Y-m-d H:i:s', (int) $counts['_last_at'])); ?>
            <?php endif; ?>
        </p>

        <h2>Privacy Boundary</h2>
        <ul>
            <li>Answers are not stored in analytics.</li>
            <li>Raw scores are rejected.</li>
            <li>Tier tables and hidden routing are rejected.</li>
            <li>API keys and token secrets are rejected.</li>
            <li>This admin page requires <code>manage_options</code>.</li>
        </ul>
    </div>

    <style>
        .emergence-admin-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin: 18px 0 24px;
        }

        .emergence-admin-metric-card {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }

        .emergence-admin-metric-card span {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #646970;
            margin-bottom: 8px;
        }

        .emergence-admin-metric-card strong {
            display: block;
            font-size: 32px;
            line-height: 1.1;
        }

        .emergence-admin-metric-card p {
            margin: 8px 0 0;
            color: #646970;
        }

        .emergence-admin-table {
            max-width: 900px;
        }

        @media (max-width: 1100px) {
            .emergence-admin-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .emergence-admin-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
}
// DREAMOS_ADMIN_EVENT_DASHBOARD_END

// DREAMOS_PUBLIC_DEMO_HARDENING_BEGIN lane 115
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'emergence_character_generator')) {
        return;
    }
    ?>
    <script id="dreamos-public-demo-hardening-inline">
    (function () {
      'use strict';

      const FORBIDDEN = [
        'scores',
        'tiers',
        'manifest_threshold',
        'flavor_vectors',
        'spark_signature',
        'combat_capability',
        'answers',
        'showwork',
        'raw_roll',
        'odds:',
        'api_key'
      ];

      function esc(value) {
        return String(value == null ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function safe(text) {
        const lower = String(text || '').toLowerCase();
        FORBIDDEN.forEach(function (key) {
          if (lower.indexOf(key) !== -1) {
            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
          }
        });
      }

      function root() {
        return document.querySelector('#emergence-character-generator, .emergence-character-generator, .ecg-shell, .ecg-app, .ecg-wrap, [data-emergence-character-generator]') ||
          document.querySelector('[class*="ecg-"], [class*="emergence"]') ||
          document.body;
      }

      function bodyText() {
        return String(document.body ? document.body.innerText || '' : '');
      }

      function hasText(needle) {
        return bodyText().toLowerCase().indexOf(String(needle).toLowerCase()) !== -1;
      }

      function track(eventName, context) {
        if (typeof window.DreamOSEmergenceTrackEvent === 'function') {
          window.DreamOSEmergenceTrackEvent(eventName, context || {});
        }
      }

      function ensureDemoGuide() {
        const scope = root();
        if (!scope || document.getElementById('dreamos-demo-flow-guide')) {
          return;
        }

        const guide = document.createElement('section');
        guide.id = 'dreamos-demo-flow-guide';
        guide.className = 'ecg-demo-flow-guide';
        guide.innerHTML = [
          '<p class="ecg-kicker">Playable Prototype</p>',
          '<h2>Create, Save, Share, and Battle Your Spark</h2>',
          '<ol>',
          '<li><strong>Scan your Spark.</strong> Answer the first questions, then continue without losing progress.</li>',
          '<li><strong>Name and style the hero.</strong> Add a costume concept, personality, and ability showcase.</li>',
          '<li><strong>Copy the portrait prompt.</strong> Use the polished prompt for premium comic-style art.</li>',
          '<li><strong>Save and share.</strong> Reload links and battle links are generated after save.</li>',
          '<li><strong>Battle the character.</strong> Send the saved Spark into the simulator.</li>',
          '</ol>',
          '<p class="ecg-demo-note">Progress is protected in this browser session. If the page refreshes, your visible draft answers are restored.</p>'
        ].join('');

        safe(guide.textContent);
        scope.insertAdjacentElement('beforebegin', guide);
      }

      function ensureProgressStrip() {
        if (document.getElementById('dreamos-demo-progress-strip')) {
          return;
        }

        const guide = document.getElementById('dreamos-demo-flow-guide');
        if (!guide) {
          return;
        }

        const strip = document.createElement('div');
        strip.id = 'dreamos-demo-progress-strip';
        strip.className = 'ecg-demo-progress-strip';
        strip.innerHTML = [
          '<span data-step="scan">1 Scan</span>',
          '<span data-step="style">2 Style</span>',
          '<span data-step="prompt">3 Prompt</span>',
          '<span data-step="save">4 Save</span>',
          '<span data-step="battle">5 Battle</span>'
        ].join('');
        guide.insertAdjacentElement('afterend', strip);
      }

      function markStep(step) {
        document.querySelectorAll('.ecg-demo-progress-strip span').forEach(function (node) {
          if (node.getAttribute('data-step') === step) {
            node.setAttribute('data-active', '1');
          }
        });
      }

      function detectCurrentStep() {
        const text = bodyText().toLowerCase();

        if (text.indexOf('save character') !== -1 || text.indexOf('reload character') !== -1 || text.indexOf('share character') !== -1) {
          markStep('save');
        }

        if (text.indexOf('copy prompt') !== -1 || text.indexOf('premium portrait prompt') !== -1) {
          markStep('prompt');
        }

        if (text.indexOf('costume concept') !== -1 || text.indexOf('personality / attitude') !== -1) {
          markStep('style');
        }

        if (text.indexOf('battle simulator') !== -1 || text.indexOf('open in battle simulator') !== -1) {
          markStep('battle');
        }

        markStep('scan');
      }

      function ensureFrictionHelp() {
        const scope = root();
        if (!scope || document.getElementById('dreamos-demo-friction-help')) {
          return;
        }

        const help = document.createElement('aside');
        help.id = 'dreamos-demo-friction-help';
        help.className = 'ecg-demo-friction-help';
        help.innerHTML = [
          '<strong>Demo tip:</strong> Finish each visible section from top to bottom. ',
          'After your final dossier appears, use <em>Copy Prompt</em>, then <em>Save Character Record</em>, then <em>Open in Battle Simulator</em>.'
        ].join('');

        safe(help.textContent);
        scope.insertAdjacentElement('afterend', help);
      }

      function hardenCopyButtons() {
        document.querySelectorAll('button').forEach(function (button) {
          const text = String(button.textContent || '').toLowerCase();
          if (text.indexOf('copy') === -1 || button.getAttribute('data-demo-copy-guard') === '1') {
            return;
          }

          button.setAttribute('data-demo-copy-guard', '1');
          button.addEventListener('click', function () {
            track('premium_prompt_copied', {button: 'demo_copy_guard'});
            button.setAttribute('aria-live', 'polite');
          }, true);
        });
      }

      function hardenSaveButtons() {
        document.querySelectorAll('button').forEach(function (button) {
          const text = String(button.textContent || '').toLowerCase();
          if (text.indexOf('save character') === -1 || button.getAttribute('data-demo-save-guard') === '1') {
            return;
          }

          button.setAttribute('data-demo-save-guard', '1');
          button.addEventListener('click', function () {
            track('character_saved', {button: 'demo_save_guard'});
          }, true);
        });
      }

      function hardenBattleLinks() {
        document.querySelectorAll('a, button').forEach(function (node) {
          const text = String(node.textContent || '').toLowerCase();
          const href = String(node.getAttribute('href') || '').toLowerCase();

          if ((text.indexOf('battle') === -1 && href.indexOf('/battles') === -1) || node.getAttribute('data-demo-battle-guard') === '1') {
            return;
          }

          node.setAttribute('data-demo-battle-guard', '1');
          node.addEventListener('click', function () {
            track('battle_opened', {button: 'demo_battle_guard'});
          }, true);
        });
      }

      function ensureSaveFallbackNote() {
        const text = bodyText().toLowerCase();
        if (document.getElementById('dreamos-save-fallback-note')) {
          return;
        }

        if (text.indexOf('save character') === -1 && text.indexOf('copy prompt') === -1) {
          return;
        }

        const anchor = document.querySelector('.ecg-prompt-preview-card, .ecg-premium-prompt-panel, .ecg-profile-card') || root();
        if (!anchor) {
          return;
        }

        const note = document.createElement('p');
        note.id = 'dreamos-save-fallback-note';
        note.className = 'ecg-demo-note';
        note.textContent = 'When your character is ready, save it first. The saved card gives you reload, share, and battle links.';
        safe(note.textContent);
        anchor.insertAdjacentElement('afterend', note);
      }

      function boot() {
        ensureDemoGuide();
        ensureProgressStrip();
        ensureFrictionHelp();
        detectCurrentStep();
        hardenCopyButtons();
        hardenSaveButtons();
        hardenBattleLinks();
        ensureSaveFallbackNote();

        if (hasText('scan') || hasText('spark')) {
          track('character_started', {phase: 'demo_hardening_seen'});
        }
      }

      document.addEventListener('DOMContentLoaded', boot);
      setInterval(boot, 1200);
      boot();

      window.DreamOSEmergenceDemoHardening = {
        boot: boot,
        markStep: markStep
      };
    })();
    </script>
    <style id="dreamos-public-demo-hardening-style">
      .ecg-demo-flow-guide {
        margin: 1rem 0 1.25rem;
        padding: 1.1rem;
        border-radius: 24px;
        border: 1px solid rgba(255,255,255,.18);
        background: linear-gradient(135deg, rgba(255,255,255,.11), rgba(255,255,255,.04));
      }

      .ecg-demo-flow-guide h2 {
        margin: .2rem 0 .8rem;
      }

      .ecg-demo-flow-guide ol {
        margin: .5rem 0 0;
        padding-left: 1.2rem;
      }

      .ecg-demo-flow-guide li {
        margin: .45rem 0;
      }

      .ecg-demo-note,
      .ecg-demo-friction-help {
        margin: .8rem 0;
        padding: .8rem;
        border-radius: 16px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.14);
      }

      .ecg-demo-progress-strip {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        margin: .75rem 0 1rem;
      }

      .ecg-demo-progress-strip span {
        border-radius: 999px;
        padding: .45rem .7rem;
        border: 1px solid rgba(255,255,255,.16);
        background: rgba(255,255,255,.055);
        font-weight: 800;
        font-size: .85rem;
      }

      .ecg-demo-progress-strip span[data-active="1"] {
        background: rgba(255,255,255,.18);
      }
    </style>
    <?php
});
// DREAMOS_PUBLIC_DEMO_HARDENING_END
