# Spark Protocol — Deterministic Engine

A modular PHP implementation of the computable subsystems of the Spark
Protocol universe. It turns the four design documents into tested,
composable code:

| Document | Subsystem | Status |
| --- | --- | --- |
| `Protocol_v8_5` | Sheet generation (Steps 2–11) + **Combat Capability** (Step 9) | code |
| `Exchange_ShopV2` | The Exchange — capacity-gated requisition | code |
| `Battle_SimV2` | Battle odds (Step 1) + outcome lottery (Step 2) | code |
| `DossierV2` | In-world handbook writer | *prompt, not code* |

The dossier writer and the fight **narration** are LLM-prompt tasks, not
deterministic logic, so they are intentionally **not** ported. Everything
here is the math the prompts sit on top of — the part that can be pinned
with tests.

## Why this shape

The protocol's own rule is "the sheet is frozen truth; you never recompute
it." That maps cleanly onto immutable value objects (`Power`, `Sheet`) and
stateless engines (`CombatCapability`, `Exchange`, `OddsAssessment`). Each
subsystem is independently testable and shares one source of truth,
`PowerRegistry`, so a power's weight/role/axis is defined exactly once.

```
src/
  Model/      Power, Sheet, ThreatClass, PowerRegistry, DomainRegistry
  Engine/     CombatCapability (Step 9), SheetGenerator (Steps 2–11), DomainScores
  Exchange/   CapacityCost (fixed table), Exchange (requisition), results
  Battle/     OddsAssessment, OutcomeLottery, ArenaRoller, BattleSimulator,
              EffectiveTier, TierMatchup, HardCounters, StrategicTags, Ambush
  Support/    Rng (interface), MtRng (prod), SequenceRng (tests)
tests/
  Unit/  Integration/
bin/
  demo.php          end-to-end walkthrough
  run-tests.php     zero-dependency test runner
```

## Test-driven

Every engine was written test-first, with the protocol's own **worked
reads and anchors** transcribed as assertions:

- **Combat Capability** — Anchor 1 (the 91), Anchor 2 (dual-T5 = 100), the
  lone-heavy 50, the scaled arbiter 77, the solo-Telepath floor 38, etc.
- **The Exchange** — both anchors, the equal-paths-to-90 invariant, the
  monotonic +3/+4/+9/+12 column, over-capacity refusal, the third-T5 ban.
- **Odds** — the ±25 swing cap, "a 90/10 gap can't be inverted by terrain",
  symmetry under swapping A/B, ambush direction.
- **Lottery** — commit-before-roll, band reading, convergence to the
  assessed odds over many runs.

> Note: the Step 9 floor rule in the source is internally inconsistent for
> a lone indirect power (the worked answer 38 contradicts "the higher of
> floor and raw read", which would give 40). The engine resolves it the way
> the worked example intends — an empty spine slot takes its center from the
> unrounded `tier × multiplier × 10` product — which reproduces **all**
> published anchors. See `CombatCapability::read()`.

## Running the tests

With Composer + PHPUnit (recommended):

```bash
composer install
composer test          # or: vendor/bin/phpunit
```

Without any dependencies (only the PHP CLI):

```bash
php bin/run-tests.php
php bin/run-tests.php --filter=CombatCapability
```

The test files are identical under both runners: they `use
Spark\Tests\TestCase`, which the bootstrap aliases to PHPUnit's `TestCase`
when PHPUnit is installed, and to a bundled lookalike otherwise.

## Quick start

```php
require 'autoload.php';

use Spark\Engine\{DomainScores, SheetGenerator};
use Spark\Exchange\Exchange;
use Spark\Battle\BattleSimulator;
use Spark\Support\MtRng;

// Generate a locked sheet from scored domains + flavor vectors.
$scores = new DomainScores(['Titan' => 22, 'Mind' => 14, /* ... */]);
$hero = SheetGenerator::generate('Bulwark', $scores, [
    'Titan' => ['Super Strength' => 5, 'Invulnerability' => 3],
]);

// Requisition a new power between engagements (re-locks the sheet).
$hero = (new Exchange())->buyNewPower($hero, 'Flight', 2)->sheet();

// Simulate one fight (arena roll, then a separate outcome roll).
$report = (new BattleSimulator(new MtRng()))->simulate($hero, $villain);
echo $report->winnerName();
echo $report->showWork();   // operator-only; never shown to players
```

See `bin/demo.php` for a full walkthrough.

## Design notes

- **`Rng` is injectable.** Production uses `MtRng` (`random_int`); tests use
  `SequenceRng`. The outcome roll only happens *after* the `Odds` object
  exists, so the "commit the threshold before the number exists" safeguard
  is structural, not a convention.
- **Weight is never passed in.** `Power` derives its weight from
  `PowerRegistry`, so a sheet can't contradict the rulebook.
- **The Exchange never recomputes CC.** It takes the locked CC and *adds*
  the published capacity cost, capping at 100 — exactly as the document
  requires, so the storefront gauge and the sheet never disagree.
- **City-wide is the ceiling.** No subsystem produces anything above the
  documented caps (CC 100, tier T5 on a sheet, effective T6 only in live
  resolution).
