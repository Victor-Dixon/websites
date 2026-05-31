<?php
/**
 * Plugin Name: Spark Battle Sim
 * Description: Cinematic Spark Protocol battle simulator shortcode.
 * Version: 0.2.8
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

// Dream.OS character-to-battle handoff lane 098.
add_action('wp_enqueue_scripts', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'spark_battle_sim')) {
        return;
    }

    $base = plugin_dir_url(__FILE__) . 'assets/';
    wp_enqueue_style('spark-battle-sim-handoff', $base . 'battle-handoff.css', array(), '0.2.2');
    wp_enqueue_script('spark-battle-sim-handoff', $base . 'battle-handoff.js', array(), '0.2.2', true);
});

// dreamos-bs-handoff-public-asset-guard lane 098d
add_action('wp_enqueue_scripts', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'spark_battle_sim')) {
        return;
    }

    $base = plugin_dir_url(__FILE__) . 'assets/';
    wp_enqueue_style('spark-battle-sim-handoff-public', $base . 'battle-handoff.css', array(), '0.2.3');
    wp_enqueue_script('spark-battle-sim-handoff-public', $base . 'battle-handoff.js', array(), '0.2.3', true);
});

// DREAMOS_BATTLE_HANDOFF_INLINE_BEGIN lane 098e
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'spark_battle_sim')) {
        return;
    }
    ?>
    <script id="dreamos-bs-battle-handoff-inline">
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

      function esc(value) {
        return String(value == null ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function readPayload() {
        const raw = window.localStorage.getItem(STORAGE_KEY);
        if (!raw) {
          return null;
        }

        FORBIDDEN.forEach(function (key) {
          if (raw.indexOf(key) !== -1) {
            throw new Error('Unsafe handoff payload blocked: ' + key);
          }
        });

        const payload = JSON.parse(raw);
        if (!payload || payload.version !== 1 || payload.source !== 'emergence-character-generator') {
          return null;
        }

        return payload;
      }

      function renderPayload(payload) {
        const powers = (payload.selected_powers || []).map(function (power) {
          return '<li>' + esc(power.power || 'Unknown ability') + '</li>';
        }).join('');

        return [
          '<section class="sbs-handoff-card" data-spark-handoff="1">',
          '<p class="sbs-handoff-kicker">Imported Spark</p>',
          '<h2>' + esc(payload.spark_name || payload.title || 'Unnamed Spark') + '</h2>',
          payload.archetype ? '<p><strong>' + esc(payload.archetype) + '</strong></p>' : '',
          payload.summary ? '<p>' + esc(payload.summary) + '</p>' : '',
          powers ? '<h3>Manifested Abilities</h3><ul>' + powers + '</ul>' : '',
          '<p class="sbs-handoff-note">Player-safe handoff loaded. Backend scoring remains hidden.</p>',
          '</section>'
        ].join('');
      }

      function mount() {
        let payload = null;
        try {
          payload = readPayload();
        } catch (error) {
          console.warn('[SparkBattleSim] unsafe handoff ignored');
          return;
        }

        if (!payload || document.querySelector('[data-spark-handoff="1"]')) {
          return;
        }

        const root = document.querySelector('main, article, form, body');
        if (!root) {
          return;
        }

        const wrapper = document.createElement('div');
        wrapper.innerHTML = renderPayload(payload);

        if (root === document.body) {
          document.body.insertBefore(wrapper.firstElementChild, document.body.firstChild);
        } else {
          root.insertBefore(wrapper.firstElementChild, root.firstChild);
        }
      }

      document.addEventListener('DOMContentLoaded', mount);
      mount();
    })();
    </script>
    <style id="dreamos-bs-battle-handoff-inline-style">
      .sbs-handoff-card {
        margin: 1.25rem 0;
        padding: 1.15rem;
        border-radius: 22px;
        border: 1px solid rgba(255,255,255,.18);
        background: rgba(255,255,255,.055);
      }
      .sbs-handoff-kicker {
        text-transform: uppercase;
        letter-spacing: .12em;
        font-size: .8rem;
        opacity: .75;
        margin: 0 0 .35rem;
      }
      .sbs-handoff-note {
        opacity: .85;
        font-weight: 700;
      }
    </style>
    <?php
});
// DREAMOS_BATTLE_HANDOFF_INLINE_END

// DREAMOS_BATTLE_CINEMATICS_BEGIN lane 106b
function spark_battle_sim_cinematic_pick($items, $seed) {
    if (!is_array($items) || !count($items)) {
        return '';
    }
    return $items[abs(crc32((string) $seed)) % count($items)];
}

function spark_battle_sim_power_phrase($powers) {
    if (!is_array($powers) || !count($powers)) {
        return 'latent force gathering below the surface';
    }

    $clean = array();
    foreach ($powers as $power) {
        $label = sanitize_text_field((string) $power);
        if ($label) {
            $clean[] = $label;
        }
    }

    if (!count($clean)) {
        return 'latent force gathering below the surface';
    }

    return implode(', ', array_slice($clean, 0, 3));
}

function spark_battle_sim_cinematic_role($archetype, $powers) {
    $text = strtolower((string) $archetype . ' ' . implode(' ', is_array($powers) ? $powers : array()));

    if (strpos($text, 'light') !== false || strpos($text, 'laser') !== false || strpos($text, 'hard light') !== false) {
        return 'radiant striker';
    }
    if (strpos($text, 'shadow') !== false || strpos($text, 'void') !== false || strpos($text, 'specter') !== false) {
        return 'threshold stalker';
    }
    if (strpos($text, 'titan') !== false || strpos($text, 'guardian') !== false || strpos($text, 'armor') !== false) {
        return 'frontline bulwark';
    }
    if (strpos($text, 'velocity') !== false || strpos($text, 'speed') !== false) {
        return 'rapid-entry duelist';
    }
    if (strpos($text, 'mind') !== false || strpos($text, 'psychic') !== false || strpos($text, 'will') !== false) {
        return 'mental pressure fighter';
    }
    if (strpos($text, 'primal') !== false || strpos($text, 'beast') !== false || strpos($text, 'instinct') !== false) {
        return 'instinct-driven predator';
    }
    if (strpos($text, 'energy') !== false || strpos($text, 'burn') !== false || strpos($text, 'plasma') !== false) {
        return 'high-output blaster';
    }

    return 'adaptive Spark combatant';
}

function spark_battle_sim_cinematic_arena($name, $opponent_name, $powers) {
    $seed = $name . '|' . $opponent_name . '|' . implode(',', is_array($powers) ? $powers : array());

    $arenas = array(
        array('name' => 'storm-lit rooftop relay', 'detail' => 'rain lashes across antenna towers while every impact throws white sparks into the dark'),
        array('name' => 'collapsed civic plaza', 'detail' => 'broken marble, emergency lights, and drifting dust turn every movement into a silhouette'),
        array('name' => 'neon transit platform', 'detail' => 'passing trains split the battlefield into pulses of light, shadow, and timing windows'),
        array('name' => 'glass-walled command deck', 'detail' => 'fractured reflections make every feint look like a squad of ghosts'),
        array('name' => 'flooded underpass', 'detail' => 'ankle-deep water carries ripples from every step before the strike arrives'),
        array('name' => 'burned-out training yard', 'detail' => 'old impact craters and scorched barriers give both fighters cover, lanes, and traps')
    );

    return spark_battle_sim_cinematic_pick($arenas, $seed);
}

function spark_battle_sim_cinematic_story($name, $opponent_name, $archetype, $summary, $powers, $winner, $arena) {
    $power_phrase = spark_battle_sim_power_phrase($powers);
    $role = spark_battle_sim_cinematic_role($archetype, $powers);
    $seed = $name . '|' . $opponent_name . '|' . $winner . '|' . $arena['name'];

    $openers = array(
        "$name steps into the {$arena['name']} as a $role, letting the first beat of the fight reveal nothing but posture.",
        "$name arrives under the pressure of the {$arena['name']}, quiet until the field itself starts reacting.",
        "The moment $opponent_name enters the {$arena['name']}, $name shifts from saved dossier to living threat.",
        "$name does not announce the opening move. The {$arena['name']} does it for them."
    );

    $clashes = array(
        "The first exchange tears through {$arena['detail']}. $power_phrase shapes the rhythm, forcing $opponent_name to answer movement with commitment.",
        "The arena narrows into lanes of consequence. $power_phrase flashes through the conflict, not as spectacle, but as control.",
        "$opponent_name presses forward, but $name turns the terrain into a weapon: {$arena['detail']}.",
        "Every feint becomes a test. $power_phrase gives $name a visible combat identity while the backend math stays sealed."
    );

    $turns = array(
        "The turning point comes when $name stops reacting and starts dictating range.",
        "$opponent_name finds an opening, but it is the wrong opening — the kind $name wanted seen.",
        "The fight pivots when the arena stops being background and becomes part of $name's timing.",
        "For one breath, both fighters understand the same truth: the next clean action decides it."
    );

    if ($winner === $name) {
        $finishers = array(
            "$name closes the battle with a decisive cinematic beat, leaving $opponent_name beaten but readable in the aftermath.",
            "$name wins by converting identity into action — power, timing, and presence landing together.",
            "$name takes the final exchange and stands in the aftermath as the arena settles around the result.",
            "$name claims the win through the story the battle made visible."
        );
    } else {
        $finishers = array(
            "$opponent_name survives the pressure, finds the final counter, and takes the win from the edge of collapse.",
            "$opponent_name wins by forcing $name into one exchange too many.",
            "$opponent_name claims the last beat, turning the arena's chaos against $name.",
            "$opponent_name takes the result, but $name leaves a clear combat signature behind."
        );
    }

    $summary_line = trim((string) $summary);
    if ($summary_line) {
        $summary_line = ' Profile read: ' . sanitize_text_field($summary_line);
    }

    return implode(' ', array(
        spark_battle_sim_cinematic_pick($openers, $seed . '|open'),
        spark_battle_sim_cinematic_pick($clashes, $seed . '|clash'),
        spark_battle_sim_cinematic_pick($turns, $seed . '|turn'),
        spark_battle_sim_cinematic_pick($finishers, $seed . '|finish'),
        $summary_line
    ));
}
// DREAMOS_BATTLE_CINEMATICS_END

// DREAMOS_CUSTOM_SPARK_BATTLE_REST_BEGIN lane 099
add_action('rest_api_init', function () {
    register_rest_route('spark-battle/v1', '/custom-battle', array(
        'methods' => 'POST',
        'callback' => 'spark_battle_sim_custom_battle_rest',
        'permission_callback' => '__return_true',
    ));
});

function spark_battle_sim_custom_battle_rest($request) {
    $params = $request->get_json_params();
    if (!is_array($params)) {
        $params = array();
    }

    $spark = isset($params['spark']) && is_array($params['spark']) ? $params['spark'] : array();
    $opponent = isset($params['opponent']) ? sanitize_text_field($params['opponent']) : 'the-victor';

    $forbidden = array(
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
    );

    $serialized = wp_json_encode($spark);
    foreach ($forbidden as $key) {
        if (strpos($serialized, $key) !== false) {
            return new WP_REST_Response(array(
                'status' => 'blocked',
                'message' => 'Unsafe custom Spark payload blocked.',
            ), 400);
        }
    }

    $name = isset($spark['spark_name']) ? sanitize_text_field($spark['spark_name']) : '';
    if (!$name && isset($spark['title'])) {
        $name = sanitize_text_field($spark['title']);
    }
    if (!$name) {
        $name = 'Imported Spark';
    }

    $powers = array();
    if (isset($spark['selected_powers']) && is_array($spark['selected_powers'])) {
        foreach ($spark['selected_powers'] as $power) {
            if (!is_array($power)) {
                continue;
            }
            $label = isset($power['power']) ? sanitize_text_field($power['power']) : '';
            if ($label) {
                $powers[] = $label;
            }
        }
    }

    $opponent_names = array(
        'the-victor' => 'The Victor',
        'captain-cap-wilson' => 'Captain Cap Wilson',
        'captain-cap' => 'Captain Cap Wilson',
    );

    $opponent_name = isset($opponent_names[$opponent]) ? $opponent_names[$opponent] : ucwords(str_replace('-', ' ', $opponent));

    $power_count = count($powers);
    $name_weight = strlen($name) % 9;
    $opponent_weight = strlen($opponent_name) % 7;
    $custom_score = 42 + ($power_count * 6) + $name_weight;
    $opponent_score = 48 + $opponent_weight;

    $winner = $custom_score >= $opponent_score ? $name : $opponent_name;

    $archetype = isset($spark['archetype']) ? sanitize_text_field($spark['archetype']) : 'Spark Profile';
    $summary = isset($spark['summary']) ? sanitize_textarea_field($spark['summary']) : '';

    $arena_data = spark_battle_sim_cinematic_arena($name, $opponent_name, $powers);
    $arena = $arena_data['name'];
    $story = spark_battle_sim_cinematic_story($name, $opponent_name, $archetype, $summary, $powers, $winner, $arena_data);
    $cinematic_role = spark_battle_sim_cinematic_role($archetype, $powers);
    $ability_line = spark_battle_sim_power_phrase($powers);

    return new WP_REST_Response(array(
        'status' => 'resolved',
        'mode' => 'custom_spark_battle',
        'winner' => $winner,
        'arena' => $arena,
        'story' => $story,
        'cinematic_role' => $cinematic_role,
        'ability_showcase' => $ability_line,
        'player_safe' => true,
        'math_hidden' => true,
    ), 200);
}
// DREAMOS_CUSTOM_SPARK_BATTLE_REST_END

// DREAMOS_BATTLE_TOKEN_IMPORT_INLINE_BEGIN lane 101
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'spark_battle_sim')) {
        return;
    }
    ?>
    <script id="dreamos-bs-token-handoff-inline">
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
        'odds',
        'raw'
      ];

      function tokenFromUrl() {
        const params = new URLSearchParams(window.location.search || '');
        return params.get('spark_token') || '';
      }

      function rejectUnsafe(payload) {
        const serialized = JSON.stringify(payload);
        FORBIDDEN.forEach(function (key) {
          if (serialized.indexOf(key) !== -1) {
            throw new Error('Unsafe token payload blocked: ' + key);
          }
        });
      }

      async function loadSparkToken() {
        const token = tokenFromUrl();
        if (!token) {
          return;
        }

        const response = await fetch('/wp-json/emergence/v1/spark-token/' + encodeURIComponent(token), {
          method: 'GET',
          headers: {'Accept': 'application/json'}
        });

        const data = await response.json();
        if (!response.ok || data.status !== 'loaded' || !data.spark) {
          const panel = document.createElement('section');
          panel.className = 'sbs-handoff-card';
          panel.innerHTML = '<p class="sbs-handoff-note">Spark token rejected or expired.</p>';
          document.body.insertBefore(panel, document.body.firstChild);
          return;
        }

        rejectUnsafe(data.spark);
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data.spark));
      }

      loadSparkToken().catch(function () {
        console.warn('[SparkBattleSim] token handoff rejected');
      });
    })();
    </script>
    <?php
});
// DREAMOS_BATTLE_TOKEN_IMPORT_INLINE_END

// DREAMOS_BATTLE_RECORD_IMPORT_INLINE_BEGIN lane 103
add_action('wp_footer', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post || !isset($post->post_content) || !has_shortcode($post->post_content, 'spark_battle_sim')) {
        return;
    }
    ?>
    <script id="dreamos-bs-record-handoff-inline">
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
        'odds',
        'raw'
      ];

      function recordFromUrl() {
        const params = new URLSearchParams(window.location.search || '');
        return params.get('character_record') || '';
      }

      function rejectUnsafe(payload) {
        const serialized = JSON.stringify(payload);
        FORBIDDEN.forEach(function (key) {
          if (serialized.indexOf(key) !== -1) {
            throw new Error('Unsafe character record payload blocked: ' + key);
          }
        });
      }

      async function loadCharacterRecord() {
        const record = recordFromUrl();
        if (!record) {
          return;
        }

        const response = await fetch('/wp-json/emergence/v1/characters/' + encodeURIComponent(record), {
          method: 'GET',
          headers: {'Accept': 'application/json'}
        });

        const data = await response.json();
        if (!response.ok || data.status !== 'loaded' || !data.character) {
          const panel = document.createElement('section');
          panel.className = 'sbs-handoff-card';
          panel.innerHTML = '<p class="sbs-handoff-note">Character record rejected or expired.</p>';
          document.body.insertBefore(panel, document.body.firstChild);
          return;
        }

        rejectUnsafe(data.character);
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(data.character));
      }

      loadCharacterRecord().catch(function () {
        console.warn('[SparkBattleSim] character record handoff rejected');
      });
    })();
    </script>
    <?php
});
// DREAMOS_BATTLE_RECORD_IMPORT_INLINE_END
