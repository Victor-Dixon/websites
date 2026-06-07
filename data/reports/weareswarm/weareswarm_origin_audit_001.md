# WeAreSwarm Origin Audit

generated=2026-06-05T14:55:16.9074400-05:00
root=D:\websites

## Verdict

**Yours.** Real domain on Hostinger. Real repo traces. **Live Activity feed is static marketing HTML, not live telemetry.**

## Git Remote

origin	git@github.com:Victor-Dixon/websites.git (fetch) origin	git@github.com:Victor-Dixon/websites.git (push)

## Current Branch

master

## Recent WeAreSwarm Commits (sample)

ebd3684c 2026-06-03 18:21:48 -0500 Document WeAreSwarm closeout dispatch block e05e9a45 2026-06-03 18:20:23 -0500 Document WeAreSwarm closeout dispatch c01ada8a 2026-06-03 18:16:10 -0500 Add WeAreSwarm live proof feed card fda2251c 2026-06-03 18:13:28 -0500 Document WeAreSwarm live proof f34e852c 2026-06-03 18:09:32 -0500 Document WeAreSwarm root htaccess redirect repair dc47089d 2026-06-03 18:07:54 -0500 Document WeAreSwarm root services repair bc686c95 2026-06-03 18:03:46 -0500 Document WeAreSwarm services route 403 repair e0fa88c4 2026-06-03 18:01:42 -0500 Document WeAreSwarm DreamOS services deploy 74a70f4b 2026-06-03 17:50:21 -0500 Document WeAreSwarm deploy auth diagnostics 761f571e 2026-06-03 17:48:31 -0500 Document WeAreSwarm SSH auth block 4fa3c836 2026-06-03 17:39:51 -0500 Document WeAreSwarm SFTP preflight block f827de8b 2026-06-03 17:36:25 -0500 Diagnose WeAreSwarm domain health 5566587f 2026-06-03 17:34:42 -0500 Document WeAreSwarm live domain probe 24f5a61b 2026-06-03 17:33:05 -0500 Document WeAreSwarm remote target selection e8f300f5 2026-06-03 17:25:56 -0500 Select WeAreSwarm live upload method 09e6f526 2026-06-03 17:23:47 -0500 Document WeAreSwarm deploy capability probe aced9a24 2026-06-03 17:22:22 -0500 Document WeAreSwarm deploy target fae31f90 2026-06-03 17:21:50 -0500 Package WeAreSwarm DreamOS services deploy artifact 6f7bf2b3 2026-06-03 17:18:35 -0500 Build WeAreSwarm DreamOS services funnel 2998f552 2026-03-21 12:10:21 -0500 Merge pull request #55 from Victor-Dixon/codex/execute-phase-3-deployment-and-cache-clear

## Feed Provenance

### Homepage Live Build Feed
- Hardcoded HTML in WordPress theme (see docs/evidence/phase3/2026-03-24/weareswarm.online_smoke.html)
- Same feed items (Tier 1 Quick Wins, Discord Bot fix, etc.) with manually updated date badges
- Mar 24 smoke capture showed date **Mar 24, 2026**; live site (Jun 2026) shows **Jun 05, 2026** with same items
- **Not** connected to runtime/feeds/closeouts JSON

### Closeout Proof Feed (repo artifact)
- Commit c01ada8a: Add WeAreSwarm live proof feed card
- Source: runtime/feeds/closeouts/weareswarm_live_proof_001.json
- Status LIVE_PROOF_PASS for weareswarm.site DreamOS services funnel deploy
- Next lane noted: wire feed cards to Discord/GitHub Architect dispatcher

## Template Leftovers (live)

- /history/ — restaurant template (Flavio Giuseppe, lorem ipsum, opened 1995)
- Confirms incomplete WordPress cleanup, not third-party impersonation

## Deploy Path

Victor-Dixon/websites → sites/production/websites/weareswarm.online → Hostinger WordPress (REST API + SFTP per config/site_configs.json)

## Trust Model

| Surface | Trust level |
|---------|-------------|
| Domain ownership | Verified (your Hostinger + repo) |
| GitHub/dadudekc links | Intentional ecosystem cross-links |
| Live Build Feed | Static proof/marketing card — do not treat as real-time agent telemetry |
| Closeout JSON feeds | Deploy verification artifacts — trustworthy for deploy events only |
| Restaurant pages | Stale template junk — should be removed or noindexed |

