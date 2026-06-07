# Bust Spark generator asset cache

Generated: 2026-06-05T02:20:45-05:00

## Version

`0.7.4-dossier-hotfix-001`

## Verification

```text
5: * Version: 0.7.4-dossier-hotfix-001
480:    <section class="emergence-cg">
499:        <form id="emergence-cg-form" class="ecg-form">
525:        <div id="emergence-cg-result" class="ecg-result" aria-live="polite">
529:        <div id="emergence-cg-flavor" class="ecg-flavor" data-phase="locked">
540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.4-dossier-hotfix-001');
541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.4-dossier-hotfix-001', true);
543:    wp_localize_script('emergence-cg-script', 'EmergenceCG', array(
550:add_action('wp_enqueue_scripts', 'emergence_cg_register_assets');
557:            wp_enqueue_style('emergence-cg-style');
558:            wp_enqueue_script('emergence-cg-script');
919:add_action('wp_enqueue_scripts', function () {
930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.4-dossier-hotfix-001');
931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.4-dossier-hotfix-001', true);
1035:          source: 'emergence-character-generator',
1212:        'source' => 'emergence-character-generator',
1417:        'source' => 'emergence-character-generator',
2161:        '#emergence-character-generator',
2162:        '.emergence-character-generator',
2166:        '[data-emergence-character-generator]'
2756:        wp_die(esc_html__('Admin access required.', 'emergence-character-generator'));
2941:        return document.querySelector('#emergence-character-generator, .emergence-character-generator, .ecg-shell, .ecg-app, .ecg-wrap, [data-emergence-character-generator]') ||
```

## Result

Plugin asset version was bumped so mobile browsers stop using cached `0.7.3` JS/CSS.
