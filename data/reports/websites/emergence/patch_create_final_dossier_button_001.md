# Patch Create Final Dossier button fallback

Generated: 2026-06-05T02:02:14-05:00

## Verification

```text
980:/* DreamOS Final Dossier Button Guard
420:      '<button type="button" data-ecg-action="create-final-dossier">Create Final Dossier</button>',
981: * Purpose: prevent the visible "Create Final Dossier" button from becoming a dead UI endpoint.
1088:    var old = mount.querySelector(".dreamos-final-dossier-output");
1103:    panel.className = "dreamos-final-dossier-output";
523:/* DreamOS Final Dossier Button Guard Styles */
```

## Result

Added a delegated click guard for the visible Create Final Dossier button. If native JS fails to render a result, the guard calls the healthy Spark Protocol generate endpoint and renders a visible dossier panel.
