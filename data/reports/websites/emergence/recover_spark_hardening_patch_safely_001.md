# Recover Spark hardening patch safely

Generated: 2026-06-05T06:35:04-05:00

## Baseline grep

```text
5: * Version: 0.7.7-floating-dossier-fab-001
540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.7-floating-dossier-fab-001');
541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.7-floating-dossier-fab-001', true);
550:add_action('wp_enqueue_scripts', 'emergence_cg_register_assets');
558:            wp_enqueue_script('emergence-cg-script');
919:add_action('wp_enqueue_scripts', function () {
930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.7-floating-dossier-fab-001');
931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.7-floating-dossier-fab-001', true);
2935:            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
```

## Patch verification

```text
runtime/plugins/emergence-character-generator/emergence-character-generator.php:5: * Version: 0.7.8-public-generate-payload-001
runtime/plugins/emergence-character-generator/emergence-character-generator.php:96:function emergence_cg_score_domains($answers) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:104:        $letter = isset($answers[$q - 1]) ? strtoupper(sanitize_text_field($answers[$q - 1])) : '';
runtime/plugins/emergence-character-generator/emergence-character-generator.php:231:function emergence_cg_score_flavor($flavor_answers, $manifested_domains) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:252:        $letter = isset($flavor_answers[$q]) ? strtoupper(sanitize_text_field($flavor_answers[$q])) : '';
runtime/plugins/emergence-character-generator/emergence-character-generator.php:325:function emergence_cg_domain_pass($answers) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:326:    $scores = emergence_cg_score_domains($answers);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:347:        'answers_expected' => 28,
runtime/plugins/emergence-character-generator/emergence-character-generator.php:408:function emergence_cg_final_pass($domain_answers, $flavor_answers) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:409:    $base = emergence_cg_domain_pass($domain_answers);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:413:    $vectors = emergence_cg_score_flavor($flavor_answers, $manifested);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:451:function emergence_cg_generate($domain_answers, $flavor_answers = null) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:452:    if (is_array($flavor_answers) && count($flavor_answers) > 0) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:453:        return emergence_cg_final_pass($domain_answers, $flavor_answers);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:455:    return emergence_cg_domain_pass($domain_answers);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.8-public-generate-payload-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.8-public-generate-payload-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:573:    $answers = $request->get_param('answers');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:574:    if (!is_array($answers)) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:575:        return new WP_Error('bad_answers', 'answers must be an array', array('status' => 400));
runtime/plugins/emergence-character-generator/emergence-character-generator.php:579:    foreach ($answers as $value) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:583:    $flavor_answers = $request->get_param('flavor_answers');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:586:    if (is_array($flavor_answers)) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:587:        foreach ($flavor_answers as $q => $value) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.8-public-generate-payload-001');
runtime/plugins/emergence-character-generator/emergence-character-generator.php:931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.8-public-generate-payload-001', true);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2204:        const answers = {};
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2211:              answers[key] = field.value;
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2217:            answers[key] = !!field.checked;
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2221:          answers[key] = field.value;
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2224:        return answers;
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2232:            answers: collectAnswers()
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2252:          const answers = payload && payload.answers ? payload.answers : {};
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2255:          Object.keys(answers).forEach(function (key) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2260:              const value = answers[key];
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2406:        'answers',
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2560:        'answers',
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2772:            Privacy-safe first-party event counts. This dashboard stores counts only. It does not expose answers, raw scores, tiers, hidden routing, or backend math.
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2915:        'answers',
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2935:            if (['answers','flavor','flavor_answers','source','character_name','alias','costume','attitude','visual_tone','build_type','mask','ability_showcase'].indexOf(String(key)) === -1) {
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2936:            throw new Error('Unsafe public demo hardening payload blocked: ' + key);
runtime/plugins/emergence-character-generator/emergence-character-generator.php:2981:          '<p class="ecg-demo-note">Progress is protected in this browser session. If the page refreshes, your visible draft answers are restored.</p>'
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:778:          answers: lastDomainAnswers,
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:779:          flavor_answers: flavorAnswers
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:808:      const domainPayload = await postGenerate({answers: lastDomainAnswers});
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:876:    var answers = {};
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:878:      answers[String(i)] = ["A","B","C","D","E","F","G","H"][(i - 1) % 8];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:880:    return answers;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:934:          answers: fallbackAnswers(),
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1014:    var answers = {};
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1030:      answers[q] = val.substring(0, 1).toUpperCase();
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1036:      if (!answers[key]) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1037:        answers[key] = ["A","B","C","D","E","F","G","H"][(i - 1) % 8];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1041:    return answers;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1133:      answers: collectAnswers(mount),
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1320:    var answers = {};
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1345:      answers[String(parseInt(m[1], 10))] = val.substring(0, 1).toUpperCase();
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1349:      if (!answers[String(i)]) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1350:        answers[String(i)] = ["A","B","C","D","E","F","G","H"][(i - 1) % 8];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1354:    return answers;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1435:        answers: collectAnswers(root),
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1573:    var answers = {};
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1593:      answers[String(parseInt(m[1], 10))] = val.substring(0, 1).toUpperCase();
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1597:      if (!answers[String(i)]) answers[String(i)] = ["A","B","C","D","E","F","G","H"][(i - 1) % 8];
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1600:    return answers;
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1727:        answers: collectAnswers(root),
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1811:  function answers(scope) {
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:1922:        answers: answers(r),
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2162:        answers: collectAnswers(root),
runtime/plugins/emergence-character-generator/assets/emergence-cg.js:2164:        source: "dreamos-floating-final-dossier-fab"
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:778:          answers: lastDomainAnswers,
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:779:          flavor_answers: flavorAnswers
runtime/plugins/emergence-character-generator/assets/emergence-character-generator.js:808:      const domainPayload = await postGenerate({answers: lastDomainAnswers});
```

## PHP syntax

```text
No syntax errors detected in runtime/plugins/emergence-character-generator/emergence-character-generator.php
```
