# Planet Blue — Tactical RPG MVP

**Product:** Planet Blue — standalone browser tactical RPG on a fantasy/sci-fi blue-world.

**Canonical live URL:** https://planetblue.site/

**Boundaries (do not merge):**
- Planet Blue ≠ Scott/client lane (`dadudekc.site` Spark routes)
- Planet Blue ≠ WeAreSwarm marketing system
- Planet Blue ≠ Digital Dreamscape narrative site

## Routes

| Context | Path |
|---------|------|
| Websites SSOT (domain root) | `D:\websites\sites\production\websites\planetblue.site\` |
| Legacy demo subpath | `D:\websites\sites\production\websites\weareswarm.site\planet-blue\` |
| DreamVault mirror | `D:\DreamVault\sites\production\websites\planetblue.site\` |
| PRD | `D:\DreamVault\docs\planet-blue\PRODUCT_REQUIREMENTS_DOCUMENT.md` |
| Live URL (after deploy) | `https://planetblue.site/` |

## Open locally

```bash
cd D:\websites\sites\production\websites\planetblue.site
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

## Deploy

```bash
cd D:\websites
python ops/deployment/unified_deployer.py --site planetblue.site --dry-run
python ops/deployment/unified_deployer.py --site planetblue.site
```

SFTP target: `domains/planetblue.site/public_html/` (files at domain root, e.g. `index.html` → `/`)

## Hostinger / DNS (panel steps)

1. **Register domain on Hostinger** (if not already attached to the hosting account).
2. **Add domain** — hPanel → Websites → Add Website → use existing hosting or assign to the shared account (`u996867598`).
3. **DNS** — Point nameservers to Hostinger (if domain registered elsewhere) or confirm Hostinger A records:
   - `@` → Hostinger server IP (same as other `.site` domains, e.g. `157.173.214.121`)
   - `www` → same IP or CNAME to `@`
4. **Document root** — Ensure site document root is `domains/planetblue.site/public_html` (standard Hostinger path for a domain site).
5. **SSL** — hPanel → SSL → enable free certificate for `planetblue.site` and `www.planetblue.site`.
6. **Verify** — After deploy, open https://planetblue.site/ and confirm `index.html` loads at root.

## Phase 2 (next)

- Ability combat integration (Firebolt, Guard, etc.)
- Additional missions (Deep Caverns, Sky Spire unlock chain)
- Leveling and stat growth
- Equipment / currency shop
