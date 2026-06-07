# Add floating final dossier action button

Generated: 2026-06-05T06:26:06-05:00

## Verification

```text
2007:/* DreamOS Floating Final Dossier FAB
2153:      '<p>Floating dossier action registered. Generating now.</p>',
2194:    if (document.querySelector("[data-dreamos-floating-dossier-fab]")) return;
2199:    button.setAttribute("data-dreamos-floating-dossier-fab", "1");
985:/* DreamOS Floating Final Dossier FAB Styles */
5: * Version: 0.7.7-floating-dossier-fab-001
540:    wp_register_style('emergence-cg-style', plugins_url('assets/emergence-cg.css', __FILE__), array(), '0.7.7-floating-dossier-fab-001');
541:    wp_register_script('emergence-cg-script', plugins_url('assets/emergence-cg.js', __FILE__), array(), '0.7.7-floating-dossier-fab-001', true);
930:    wp_enqueue_style('emergence-cg-public', $base . 'emergence-character-generator.css', array(), '0.7.7-floating-dossier-fab-001');
931:    wp_enqueue_script('emergence-cg-public', $base . 'emergence-character-generator.js', array(), '0.7.7-floating-dossier-fab-001', true);
```

## Result

Added a body-level fixed dossier action button with a direct click/touch handler. It bypasses the form card and native button.
