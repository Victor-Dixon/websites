# Fix canonical Spark select options and clickability

Generated: 2026-06-05T07:15:49-05:00

## Discovery

```text
--- plugin question bank lines ---
14:    return plugin_dir_path(__FILE__) . 'assets/spark-protocol-v85-domain-key.json';
33:function emergence_cg_question_bank() {
34:    $path = plugin_dir_path(__FILE__) . 'assets/protocol-v85-question-bank.json';
36:        return array('domain_questions' => array(), 'flavor_questions' => array());
41:        return array('domain_questions' => array(), 'flavor_questions' => array());
96:function emergence_cg_score_domains($answers) {
104:        $letter = isset($answers[$q - 1]) ? strtoupper(sanitize_text_field($answers[$q - 1])) : '';
231:function emergence_cg_score_flavor($flavor_answers, $manifested_domains) {
252:        $letter = isset($flavor_answers[$q]) ? strtoupper(sanitize_text_field($flavor_answers[$q])) : '';
325:function emergence_cg_domain_pass($answers) {
326:    $scores = emergence_cg_score_domains($answers);
347:        'answers_expected' => 28,
408:function emergence_cg_final_pass($domain_answers, $flavor_answers) {
409:    $base = emergence_cg_domain_pass($domain_answers);
413:    $vectors = emergence_cg_score_flavor($flavor_answers, $manifested);
451:function emergence_cg_generate($domain_answers, $flavor_answers = null) {
452:    if (is_array($flavor_answers) && count($flavor_answers) > 0) {
453:        return emergence_cg_final_pass($domain_answers, $flavor_answers);
455:    return emergence_cg_domain_pass($domain_answers);
459:    $bank = emergence_cg_question_bank();
460:    if (!isset($bank['domain_questions']) || !is_array($bank['domain_questions'])) {
464:    foreach ($bank['domain_questions'] as $item) {
465:        if (intval($item['q']) === intval($q) && isset($item['options'][$letter])) {
466:            return $letter . '. ' . $item['options'][$letter];
474:    $bank = emergence_cg_question_bank();
475:    $questions = isset($bank['domain_questions']) && is_array($bank['domain_questions']) ? $bank['domain_questions'] : array();
512:                            <?php if (isset($item['options'][$letter])) : ?>
514:                                    <?php echo esc_html($letter . '. ' . $item['options'][$letter]); ?>
546:        'question_bank' => emergence_cg_question_bank(),
573:    $answers = $request->get_param('answers');
574:    if (!is_array($answers)) {
575:        return new WP_Error('bad_answers', 'answers must be an array', array('status' => 400));
579:    foreach ($answers as $value) {
583:    $flavor_answers = $request->get_param('flavor_answers');
586:    if (is_array($flavor_answers)) {
587:        foreach ($flavor_answers as $q => $value) {
2232:        const answers = {};
2239:              answers[key] = field.value;
2245:            answers[key] = !!field.checked;
2249:          answers[key] = field.value;
2252:        return answers;
2260:            answers: collectAnswers()
2280:          const answers = payload && payload.answers ? payload.answers : {};
2283:          Object.keys(answers).forEach(function (key) {
2288:              const value = answers[key];
2434:        'answers',
2588:        'answers',
2682:        'manage_options',
2695:            return current_user_can('manage_options');
2745:    if (!current_user_can('manage_options')) {
2783:    if (!current_user_can('manage_options')) {
2800:            Privacy-safe first-party event counts. This dashboard stores counts only. It does not expose answers, raw scores, tiers, hidden routing, or backend math.
2861:            <li>This admin page requires <code>manage_options</code>.</li>
2943:        'answers',
2963:            if (['answers','flavor','flavor_answers','source','character_name','alias','costume','attitude','visual_tone','build_type','mask','ability_showcase'].indexOf(String(key)) === -1) {
2965:          if (['answers','flavor','flavor_answers','source','character_name','alias','costume','attitude','visual_tone','build_type','mask','ability_showcase'].indexOf(String(key)) === -1) {
3012:          '<p class="ecg-demo-note">Progress is protected in this browser session. If the page refreshes, your visible draft answers are restored.</p>'
--- local JSON/question assets ---
runtime/plugins/emergence-character-generator/assets/emergence-cg.css
runtime/plugins/emergence-character-generator/assets/emergence-cg.js
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js
runtime/plugins/emergence-character-generator/assets/protocol-v85-question-bank.json
runtime/plugins/emergence-character-generator/assets/spark-protocol-v85-domain-key.json
runtime/plugins/emergence-character-generator/emergence-character-generator.php
--- JSON option shape probe ---
JSON_FILE= runtime/plugins/emergence-character-generator/assets/spark-protocol-v85-domain-key.json
TOP_KEYS= ['version', 'domains', 'domain_key', 'tier_map', 'manifest_gate']
JSON_FILE= runtime/plugins/emergence-character-generator/assets/protocol-v85-question-bank.json
TOP_KEYS= ['source', 'protocol_version', 'domain_questions', 'flavor_questions']
Q 1 KEYS ['q', 'question', 'options']
Q 1 OPTIONS_REPR {'A': 'I start organizing it — already thinking about how to make the most of it.', 'B': "It bursts out of me. I can't keep it contained and I don't try.", 'C': 'A jolt of energy — I want to be up and moving.', 'D': 'A quiet, settled feeling, like something heavy clicked into place.', 'E': 'I look around at everyone else, reading whether they feel it too.', 'F': 'I keep it to myself for a while before I let anyone know.', 'G': 'I turn it over inwardly, savoring exactly what it means before anything else.', 'H': 'A kind of clarity — like a light switched on and I can finally see the whole shape of things.'}
Q 10 KEYS ['q', 'question', 'options']
Q 10 OPTIONS_REPR {'A': "Hit hard and early, with everything I've got.", 'B': 'Feel out the opponent and adapt to what they show me.', 'C': 'Get there first; speed is my weapon.', 'D': 'Stay unseen and pick my moment.', 'E': 'Take control of the variables and arrange them my way.', 'F': 'Meet them head on and simply outlast and overpower them.', 'G': 'Get inside their head and turn their own intentions against them.', 'H': "Strike from where they aren't looking, with a precision that arrives like fate."}
Q 11 KEYS ['q', 'question', 'options']
Q 11 OPTIONS_REPR {'A': "I bounce off and keep moving; I don't dwell.", 'B': 'I step back, reassess, and re-plan around the hit.', 'C': "I absorb it and don't break.", 'D': 'I withdraw, regroup quietly, and re-emerge later.', 'E': 'I get more fired up the harder things push.', 'F': "I change myself to survive what's coming.", 'G': 'I shield my mind, stay clear, and refuse to be shaken.', 'H': 'I bend around it like light — still present, just arriving from another angle.'}
Q 12 KEYS ['q', 'question', 'options']
Q 12 OPTIONS_REPR {'A': 'I shoulder it and refuse to let it slip.', 'B': 'I break it into parts and manage each one.', 'C': 'I stay loose and handle it by feel as it evolves.', 'D': "I pour myself into it completely until it's done.", 'E': 'I knock it out fast before it can pile up.', 'F': "I quietly handle it alone without making it anyone's business.", 'G': 'I hold the whole of it clear in my mind and bring others around to my view.', 'H': 'I carry it in the open for all to see, or bury it so deep only I feel the weight.'}
```

## Local verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.8.8-canonical-select-options-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.8.8-canonical-select-options-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.8.8-canonical-select-options-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:958:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.8.8-canonical-select-options-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:959:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.8.8-canonical-select-options-001', true);
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3527:/* DreamOS Canonical Spark Select Option Hardener
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3630:    select.setAttribute("data-dreamos-select-hardened", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:3673:      card.setAttribute("data-dreamos-select-options-fixed", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2123:/* DreamOS Canonical Spark Select Option Hardener
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2226:    select.setAttribute("data-dreamos-select-hardened", "1");
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:2269:      card.setAttribute("data-dreamos-select-options-fixed", "1");
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:628:  appearance: auto;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:755:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:768:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:819:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:940:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1007:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1441:/* DreamOS Canonical Spark Select Option Hardener */
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1446:select[data-dreamos-select-hardened="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1447:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1454:select[data-dreamos-select-hardened="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1456:  appearance: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-cg.css:1466:select[data-dreamos-select-hardened="1"] option {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:865:/* DreamOS Canonical Spark Select Option Hardener */
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:870:select[data-dreamos-select-hardened="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:871:  pointer-events: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:878:select[data-dreamos-select-hardened="1"] {
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:880:  appearance: auto !important;
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.css:890:select[data-dreamos-select-hardened="1"] option {
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```

## Result

Canonical renderer select controls are now rebuilt using a hardened option parser. Every Q1-Q28 select gets valid A-H values even if the question-bank copy shape varies.
