# Fix Create Final Dossier mobile clickability

Generated: 2026-06-05T02:08:39-05:00

## Problem

The final dossier button rendered but did not activate on mobile.

## Verification

```text
1189:/* DreamOS Final Dossier Touch Guard
420:      '<button type="button" data-ecg-action="create-final-dossier">Create Final Dossier</button>',
1205:    if (action === "create-final-dossier" || text.indexOf("create final dossier") !== -1 || text.indexOf("final dossier") !== -1) {
745:/* DreamOS Final Dossier Clickability Guard */
755:  pointer-events: auto !important;
768:  pointer-events: auto !important;
756:  touch-action: manipulation;
```

## Result

Added CSS and JS guards so the final dossier button is touch-safe, pointer-enabled, raised above overlays, and cannot remain disabled/inert.
