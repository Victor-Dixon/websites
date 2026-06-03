# Spark Battle Sim Protocol Inventory

Task: inventory_spark_battle_sim_for_protocol_007

## Inputs

- Plugin: `runtime/plugins/spark-battle-sim`
- Task: `runtime/tasks/emergence/integrate_spark_protocol_battle_sim_006.yaml`
- Spark Protocol package: `_reports/spark-protocol-patched-005.zip`
- Extracted Spark repo: `_work/spark-protocol-inventory-007/spark-protocol`

## Counts

- Plugin PHP files: 5
- Spark Protocol src PHP files: 29

## Current plugin files

```text
runtime/plugins/spark-battle-sim/assets/battle.css
runtime/plugins/spark-battle-sim/data/characters/captain-cap-wilson.json
runtime/plugins/spark-battle-sim/data/characters/the-victor.json
runtime/plugins/spark-battle-sim/includes/ArenaRoller.php
runtime/plugins/spark-battle-sim/includes/BattleEngine.php
runtime/plugins/spark-battle-sim/includes/CharacterRepository.php
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php
runtime/plugins/spark-battle-sim/spark-battle-sim.php
```

## Spark Protocol engine files

```text
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/Ambush.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/Arena.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/ArenaConditions.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/ArenaRoller.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/BattleReport.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/BattleSimulator.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/EffectiveTier.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/HardCounters.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/Odds.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/OddsAssessment.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/OutcomeLottery.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/OutcomeResult.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/StrategicTags.php
_work/spark-protocol-inventory-007/spark-protocol/src/Battle/TierMatchup.php
_work/spark-protocol-inventory-007/spark-protocol/src/Engine/CombatCapability.php
_work/spark-protocol-inventory-007/spark-protocol/src/Engine/DomainScores.php
_work/spark-protocol-inventory-007/spark-protocol/src/Engine/SheetGenerator.php
_work/spark-protocol-inventory-007/spark-protocol/src/Exchange/CapacityCost.php
_work/spark-protocol-inventory-007/spark-protocol/src/Exchange/Exchange.php
_work/spark-protocol-inventory-007/spark-protocol/src/Exchange/RequisitionRefused.php
_work/spark-protocol-inventory-007/spark-protocol/src/Exchange/RequisitionResult.php
_work/spark-protocol-inventory-007/spark-protocol/src/Model/DomainRegistry.php
_work/spark-protocol-inventory-007/spark-protocol/src/Model/Power.php
_work/spark-protocol-inventory-007/spark-protocol/src/Model/PowerRegistry.php
_work/spark-protocol-inventory-007/spark-protocol/src/Model/Sheet.php
_work/spark-protocol-inventory-007/spark-protocol/src/Model/ThreatClass.php
_work/spark-protocol-inventory-007/spark-protocol/src/Support/MtRng.php
_work/spark-protocol-inventory-007/spark-protocol/src/Support/Rng.php
_work/spark-protocol-inventory-007/spark-protocol/src/Support/SequenceRng.php
```

## Plugin integration seam hits

```text
