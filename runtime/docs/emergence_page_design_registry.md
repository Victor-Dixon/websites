# Emergence Page Design Registry

## Design North Star

The site is a premium Spark OS interface for a superhero awakening system.

Visual language:
- dark luminous glass
- cyan, violet, pink, and green signal gradients
- clean mobile app shell
- mission-control cards
- deterministic protocol underneath cinematic outcomes

## Canonical Routes

| Route | Role | Status | Next Patch |
|---|---|---:|---|
| / | Homepage / Spark OS landing | Active | Final screenshot polish |
| /the-emergence/ | Source content page | Active | Keep as WP source page |
| /spark-generator/ | Main player creation flow | Restored | Redesign as onboarding app |
| /spark-battle-sim/ | What-If Arena side mode | Restored | Redesign as combat console |
| /protocol/ | Protocol explanation | Legacy page exists | Convert to Protocol Lab |
| /battles/ | Battle content hub | Legacy page exists | Convert to Arena archive |
| /battle-simulator/ | Legacy route | Exists | Redirect or merge into /spark-battle-sim/ |
| /character-generator/ | Legacy generator route | Exists | Redirect or merge into /spark-generator/ |
| /build-log/ | Proof/build updates | Exists | Connect to DreamOS closeouts |
| /client-preview/ | Legacy preview | Exists | Archive or restrict |
| /emergence-preview/ | Deploy preview | Exists | Keep as preview or redirect later |

## Page Plans

### Homepage
Purpose: explain The Emergence, route users into Spark creation, and present battle sim as optional downtime mode.
Primary CTA: Generate Your Spark.
Secondary CTA: Open What-If Arena.
Next patch: tighten above-the-fold mobile screenshot until it feels like an app launch screen.

### Spark Generator
Route: /spark-generator/
Purpose: primary interactive product where users create Spark identity, profile, and power sheet.
Design direction: onboarding wizard, step cards, progress rail, identity scan panel, generate button, locked dossier result.
Next patch: wrap plugin output in Spark onboarding shell without editing plugin internals.

### Spark Battle Sim / What-If Arena
Route: /spark-battle-sim/
Purpose: optional combat projection lab and fan matchup simulator, not the core game loop.
Design direction: arena console, combatant cards, environment roll, outcome report, mobile stacked command cards.
Next patch: rename visible framing to What-If Arena and style plugin controls like a combat dashboard.

### Protocol Lab
Route: /protocol/
Purpose: explain deterministic backend rules and build trust.
Design direction: rule cards, trace examples, outcome ladder, fairness guarantees.
Next patch: replace legacy protocol content with Spark Protocol Lab page.

### Battles / Arena Archive
Route: /battles/
Purpose: archive generated battle outcomes and feed future public content loop.
Design direction: match cards, outcome badges, environment tags, replay/report links.
Next patch: turn into Arena Archive hub and link to What-If Arena.

### Build Log
Route: /build-log/
Purpose: public proof of work connected to DreamOS closeouts.
Design direction: terminal cards, commit hashes, deploy proof, feature status.
Next patch: render recent verified closeout cards, not fake marketing updates.

## Legacy Route Policy

Do not delete yet:
- /battle-simulator/
- /character-generator/
- /client-preview/
- /emergence-preview/

Policy:
- salvage before deletion
- redirect only after confirming live replacement
- keep rollback path until stable

## Patch Order

1. Spark generator visual shell
2. Battle sim / What-If Arena visual shell
3. Protocol Lab page
4. Build Log closeout feed
5. Arena Archive / Battles hub
6. Legacy route redirects
7. Global navigation final pass

## Verification Gates

Every page patch must verify:
- HTTP 200
- mobile screenshot
- no Hostinger placeholder text
- one theme header
- one theme footer
- primary CTA visible
- route role matches registry

## Status
PASS
