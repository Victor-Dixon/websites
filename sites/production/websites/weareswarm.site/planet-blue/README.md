# Planet Blue — Tactical RPG MVP

**Product:** Planet Blue — standalone browser tactical RPG on a fantasy/sci-fi blue-world.

**Boundaries (do not merge):**
- Planet Blue ≠ Scott/client lane (`dadudekc.site` Spark routes)
- Planet Blue ≠ WeAreSwarm marketing system
- Planet Blue ≠ Digital Dreamscape narrative site

## Routes

| Context | Path |
|---------|------|
| Websites SSOT | `D:\websites\sites\production\websites\weareswarm.site\planet-blue\` |
| DreamVault mirror | `D:\DreamVault\sites\production\websites\weareswarm.site\planet-blue\` |
| PRD | `D:\DreamVault\docs\planet-blue\PRODUCT_REQUIREMENTS_DOCUMENT.md` |
| Live URL (after deploy) | `https://weareswarm.site/planet-blue/` |

## Open locally

```bash
cd D:\DreamVault\sites\production\websites\weareswarm.site\planet-blue
python -m http.server 8765
```

Visit `http://localhost:8765/` — open `index.html` via the server (not file://) for consistent localStorage.

## Pages

| Page | Purpose |
|------|---------|
| `index.html` | Landing — start / continue |
| `character.html` | Name, race, class, stats, abilities |
| `map.html` | Mission nodes + profile summary |
| `battle.html` | Tactical grid combat |

## MVP scope

- 8×6 grid, grass / rock / water terrain
- Turn-based player → enemy phases
- Move, attack, wait per unit
- Enemy AI (advance + attack nearest)
- First Landing mission: 1 player, 2 enemies
- Victory: 50 XP, 25 currency (localStorage)
- 10 races, 5 classes, 5 abilities (display; combat uses basic attack)

## Verification steps

1. **All pages load** — Open each HTML page via local server; confirm no console errors.
2. **New game flow** — Start → create Human/Fire character → map shows First Landing unlocked.
3. **Battle** — Engage mission; move, attack, end turn; enemies act on enemy phase.
4. **Victory** — Defeat both enemies; overlay shows rewards; Return to Map.
5. **Persistence** — Map shows First Landing completed; profile shows 50 XP and 25 currency.
6. **Refresh** — Reload map.html; completed state and rewards remain.
7. **Continue** — index.html shows Continue button when save exists.

## Deploy

```bash
cd D:\websites
python ops/deployment/unified_deployer.py --site weareswarm.site --dry-run
```

SFTP target: `domains/weareswarm.site/public_html/planet-blue/`

## Phase 2 (next)

- Ability combat integration (Firebolt, Guard, etc.)
- Additional missions (Deep Caverns, Sky Spire unlock chain)
- Leveling and stat growth
- Equipment / currency shop
