# Dream Explorer — player sprite

Canonical Digital Dreamscape player avatar (Grid Schema V1).

## Files

| File | Purpose |
|------|---------|
| `dream-explorer.png` | 256×832 sprite sheet (4 cols × 13 rows @ 64×64) |
| `dream-explorer-atlas.json` | Animation metadata for `js/sprite-engine.js` |

## Grid layout

| Row | Action | Direction | Frames |
|-----|--------|-----------|--------|
| 0 | Idle | up / down / left / right (cols 0–3) | 1 each |
| 1–4 | Walk | up / down / left / right | 4 each |
| 5–8 | Run | up / down / left / right | 4 each |
| 9 | Interact | contextual | 2 |
| 10 | Attack | contextual | 2 |
| 11 | Magic / Cast | contextual | 2 |
| 12 | Hurt | contextual | 2 |

- **Pivot:** feet center (`pivot: { x: 0.5, y: 1.0 }`)
- **FPS:** idle 8, walk 10, run 12, actions 10

## World draw math (32px tiles)

```
anchorX = (renderX - camX) * tileSize + tileSize / 2   // tile bottom-center X
anchorY = (renderY - camY) * tileSize + tileSize       // tile bottom Y
```

SpriteEngine draws with pivot so feet align to `(anchorX, anchorY)`.

## AI regeneration prompt

> 2D chibi crystal-tech character sprite sheet, **Dream Explorer**: blue hood with diamond emblem, glowing chest core, brown boots, crystal staff. **4 columns × 13 rows**, each cell **64×64 px**, transparent or dark background. Row 0: idle up/down/left/right. Rows 1–4: walk cycle per direction (4 frames). Rows 5–8: run cycle per direction (4 frames). Rows 9–12: interact (reach cube), attack (staff slash), magic cast (floating crystals), hurt (recoil). Feet centered at bottom of each cell. No text labels on frames. Consistent proportions, clearly readable facing per row.

## Rebuild from presentation art

Source presentation PNG is rearranged by:

`D:\websites\tools\sprite-engine\scripts\build-dream-explorer-sheet.py`
