# Inject guaranteed final dossier button

Generated: 2026-06-05T02:15:22-05:00

## Verification

```text
1279:/* DreamOS Guaranteed Final Dossier Injector
1466:    if (!root || root.querySelector("[data-dreamos-guaranteed-final-dossier]")) return;
1471:    wrap.setAttribute("data-dreamos-guaranteed-final-dossier", "1");
1477:    btn.setAttribute("data-dreamos-guaranteed-final-dossier-button", "1");
1475:    btn.className = "dreamos-guaranteed-dossier-button";
800:/* DreamOS Guaranteed Final Dossier Injector Styles */
809:.dreamos-guaranteed-dossier-button {
849:  .dreamos-guaranteed-dossier-button {
```

## Result

Native final dossier button is no longer trusted. Runtime injects a fresh direct-bound button and hides the native one when found.
