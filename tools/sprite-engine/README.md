# Custom Web Sprite Engine & Asset Pipeline

**SSOT location:** `D:\websites\tools\sprite-engine\`

Standalone, dependency-free canvas sprite engine for 2D character animation. Vanilla ES modules — no Phaser, PixiJS, or bundlers required.

## Reference character: Dream Explorer

**Dream Explorer** (`assets/dream-explorer.png` + `dream-explorer-atlas.json`) is the canonical reference character for Grid Schema V1. The interactive demo loads this asset by default; append `?procedural=1` to use the runtime-generated placeholder sheet instead.

Rebuild the production grid from the presentation art:

```bash
python scripts/build-dream-explorer-sheet.py
node scripts/validate-atlas.js assets/dream-explorer-atlas.json
```

Digital Dreamscape ships a copy under `sites/production/websites/digitaldreamscape.site/assets/sprites/player/`.

## Quick start

```bash
cd D:\websites\tools\sprite-engine
python -m http.server 8080
```

Open [http://localhost:8080](http://localhost:8080). Use **WASD** / arrow keys or on-screen buttons. **Shift** = run, **Space** = attack.

## Grid Schema V1

Sprite sheets use a fixed row layout (64×64 cells recommended, 4 columns × 13 rows).

| Row | Action | Direction | Frames | Loop |
|-----|--------|-----------|--------|------|
| 0 | Idle | up / down / left / right (cols 0–3) | 1 each | Yes |
| 1 | Walk | up | 4 | Yes |
| 2 | Walk | down | 4 | Yes |
| 3 | Walk | left | 4 | Yes |
| 4 | Walk | right | 4 | Yes |
| 5 | Run | up | 4 | Yes |
| 6 | Run | down | 4 | Yes |
| 7 | Run | left | 4 | Yes |
| 8 | Run | right | 4 | Yes |
| 9 | Interact | contextual | 2 | No |
| 10 | Attack | contextual | 2 | No |
| 11 | Magic / Cast | contextual | 2 | Optional |
| 12 | Hurt / Recoil | contextual | 2 | No |

Slice coordinates: `sx = (startFrame + frameIndex) * gridW`, `sy = row * gridH`.

## JSON metadata format

```json
{
  "asset": {
    "name": "hero",
    "version": "1.0",
    "texture": "hero-sheet.png",
    "gridSize": { "w": 64, "h": 64 }
  },
  "animations": {
    "walk_down": {
      "row": 2,
      "startFrame": 0,
      "length": 4,
      "loop": true,
      "fps": 10
    }
  }
}
```

Generate the full animations block from Grid Schema V1:

```js
import { buildAtlasJson } from './js/atlas-builder.js';

const atlas = buildAtlasJson({
  name: 'my-character',
  texture: './my-sheet.png',
  gridW: 64,
  gridH: 64,
});
```

## API

```js
import { SpriteEngine } from './js/SpriteEngine.js';

const engine = new SpriteEngine(document.querySelector('canvas'), { scale: 2 });
await engine.loadAtlas('./assets/demo-atlas.json');
engine.setState('walking', 'left');
engine.play('attack');
engine.start();
```

| Method | Description |
|--------|-------------|
| `loadAtlas(jsonUrl \| object, textureUrl?)` | Fetch JSON + image |
| `loadAtlasWithImage(json, image)` | Hot-swap with preloaded image |
| `setState(action, direction)` | `idle` / `walking` / `running` + direction |
| `play(key)` | One-shot: `attack`, `interact`, `magic`, `hurt` |
| `start()` / `stop()` | 60 FPS `requestAnimationFrame` loop |
| `getDebugInfo()` | State, frame, render FPS |

Animation clips advance at per-clip `fps` (8–12 default); rendering stays at display refresh rate.

## Adding a new character asset

1. Create a PNG sprite sheet following **Grid Schema V1** (256×832 px for 64×64 cells).
2. Place PNG + JSON in `assets/` (or your project).
3. Run `node scripts/validate-atlas.js assets/your-atlas.json`.
4. Point `SpriteEngine.loadAtlas()` at your JSON.

Or auto-build JSON:

```js
import { buildAtlasJson } from './js/atlas-builder.js';
const json = buildAtlasJson({ name: 'npc-guard', texture: 'guard.png' });
```

## AI asset generation prompts

Use these hints when generating sprite sheets:

> 2D pixel-art character sprite sheet, **4 columns × 13 rows**, each cell **64×64 px**, transparent or solid dark background. Row 0: idle poses facing up/down/left/right (one frame per direction). Rows 1–4: walk cycle per direction (4 frames each). Rows 5–8: run cycle per direction (4 frames each). Rows 9–12: interact, attack, magic cast, hurt (2 frames each, left-to-right). Consistent character proportions, facing clearly readable per row. No text labels on frames.

## Validate atlas JSON

```bash
node scripts/validate-atlas.js assets/demo-atlas.json
```

## File layout

```
sprite-engine/
├── index.html          # Interactive demo
├── README.md
├── assets/
│   ├── demo-atlas.json       # Reference metadata (procedural demo)
│   ├── dream-explorer.png    # Canonical reference character sheet
│   └── dream-explorer-atlas.json
├── js/
│   ├── SpriteEngine.js
│   ├── atlas-builder.js
│   ├── demo-sprite.js  # Runtime procedural placeholder sheet
│   └── demo.js
└── scripts/
    └── validate-atlas.js
```

## Deployment

This tool is static HTML/JS. To host publicly via the websites deploy pipeline, add a static site entry under `sites/production/websites/` (e.g. `tools.sprite-engine.site`) and register it in `ops/deployment/sites.yml`. The unified deployer targets WordPress/static paths under `sites/production/websites/{domain}` — not `tools/` directly.

## Related

Planet Blue (`planetblue.site/js/sprites.js`) uses procedural tile drawing for overworld tiles — a different pattern. Import this engine into Planet Blue when battle/overworld character sheets are ready.
