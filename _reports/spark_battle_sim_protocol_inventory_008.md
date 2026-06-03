# Spark Battle Sim Protocol Inventory

Task: inventory_spark_battle_sim_for_protocol_008

## Inputs

- Plugin: `runtime/plugins/spark-battle-sim`
- Task: `runtime/tasks/emergence/integrate_spark_protocol_battle_sim_006.yaml`
- Spark Protocol package: `_reports/spark-protocol-patched-005.zip`
- Extracted Spark repo: `_work/spark-protocol-inventory-008/spark-protocol`

## Counts

- Plugin PHP files: 5
- Spark Protocol src PHP files: 29
- Plugin seam hits: 46
- Spark class/function hits: 173

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
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Ambush.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Arena.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaConditions.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaRoller.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleSimulator.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/EffectiveTier.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/HardCounters.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OddsAssessment.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeLottery.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/StrategicTags.php
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/CapacityCost.php
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionRefused.php
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php
_work/spark-protocol-inventory-008/spark-protocol/src/Model/DomainRegistry.php
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php
_work/spark-protocol-inventory-008/spark-protocol/src/Model/ThreatClass.php
_work/spark-protocol-inventory-008/spark-protocol/src/Support/MtRng.php
_work/spark-protocol-inventory-008/spark-protocol/src/Support/Rng.php
_work/spark-protocol-inventory-008/spark-protocol/src/Support/SequenceRng.php
```

## Plugin integration seam hits

```text
runtime/plugins/spark-battle-sim/includes/CharacterRepository.php:6:class Spark_Battle_CharacterRepository {
runtime/plugins/spark-battle-sim/includes/CharacterRepository.php:9:    public function __construct() {
runtime/plugins/spark-battle-sim/includes/CharacterRepository.php:13:    public function all(): array {
runtime/plugins/spark-battle-sim/includes/CharacterRepository.php:24:        uasort($characters, function ($a, $b) {
runtime/plugins/spark-battle-sim/includes/CharacterRepository.php:31:    public function get(string $slug): array {
runtime/plugins/spark-battle-sim/includes/ArenaRoller.php:6:class Spark_Battle_ArenaRoller {
runtime/plugins/spark-battle-sim/includes/ArenaRoller.php:50:    public function roll(): array {
runtime/plugins/spark-battle-sim/includes/ArenaRoller.php:51:        $location = $this->locations[random_int(0, count($this->locations) - 1)];
runtime/plugins/spark-battle-sim/includes/ArenaRoller.php:52:        $time = $this->times[random_int(0, count($this->times) - 1)];
runtime/plugins/spark-battle-sim/includes/ArenaRoller.php:53:        $weather = $this->weather[random_int(0, count($this->weather) - 1)];
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:6:class Spark_Battle_BattleEngine {
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:7:    private Spark_Battle_CharacterRepository $repo;
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:9:    public function __construct(Spark_Battle_CharacterRepository $repo) {
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:13:    public function run(string $fighter_a_slug, string $fighter_b_slug): array {
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:17:        $arena_roller = new Spark_Battle_ArenaRoller();
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:18:        $arena = $arena_roller->roll();
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:20:        $winner = $this->resolve_winner($fighter_a, $fighter_b);
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:21:        $loser = $winner['slug'] === $fighter_a['slug'] ? $fighter_b : $fighter_a;
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:23:        $renderer = new Spark_Battle_StoryRenderer();
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:30:            'winner' => $winner,
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:32:            'story' => $renderer->render($fighter_a, $fighter_b, $winner, $loser, $arena)
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:36:    private function resolve_winner(array $fighter_a, array $fighter_b): array {
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:42:        $roll = random_int(1, 100);
runtime/plugins/spark-battle-sim/includes/BattleEngine.php:44:        return $roll <= $a_threshold ? $fighter_a : $fighter_b;
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:6:class Spark_Battle_StoryRenderer {
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:7:    public function render(array $a, array $b, array $winner, array $loser, array $arena): string {
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:10:        $winner_name = $winner['name'];
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:13:        $winner_power = $this->power_line($winner);
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:22:But {$winner_name} survives the worst of it.
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:24:The final exchange is not clean. It is not easy. {$winner_name} leans into {$winner_power}, absorbs the danger, and catches the last mistake before {$loser_name} can reset with {$loser_power}.
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:26:When the dust settles, {$winner_name} is the one left standing.";
runtime/plugins/spark-battle-sim/includes/StoryRenderer.php:29:    private function power_line(array $fighter): string {
runtime/plugins/spark-battle-sim/spark-battle-sim.php:3: * Plugin Name: Spark Battle Sim
runtime/plugins/spark-battle-sim/spark-battle-sim.php:4: * Description: Cinematic Spark Protocol battle simulator shortcode.
runtime/plugins/spark-battle-sim/spark-battle-sim.php:17:require_once SPARK_BATTLE_SIM_DIR . 'includes/ArenaRoller.php';
runtime/plugins/spark-battle-sim/spark-battle-sim.php:18:require_once SPARK_BATTLE_SIM_DIR . 'includes/BattleEngine.php';
runtime/plugins/spark-battle-sim/spark-battle-sim.php:21:function spark_battle_sim_enqueue_assets() {
runtime/plugins/spark-battle-sim/spark-battle-sim.php:31:function spark_battle_sim_shortcode() {
runtime/plugins/spark-battle-sim/spark-battle-sim.php:32:    $repo = new Spark_Battle_CharacterRepository();
runtime/plugins/spark-battle-sim/spark-battle-sim.php:33:    $engine = new Spark_Battle_BattleEngine($repo);
runtime/plugins/spark-battle-sim/spark-battle-sim.php:54:                $error = 'Battle could not start.';
runtime/plugins/spark-battle-sim/spark-battle-sim.php:62:        <h2>Spark Protocol Battle Arena</h2>
runtime/plugins/spark-battle-sim/spark-battle-sim.php:91:            <button type="submit">Start Battle</button>
runtime/plugins/spark-battle-sim/spark-battle-sim.php:101:                <p><strong>Arena:</strong> <?php echo esc_html($result['arena']['summary']); ?></p>
runtime/plugins/spark-battle-sim/spark-battle-sim.php:102:                <p><strong>Result:</strong> <?php echo esc_html($result['winner']['name']); ?> is left standing.</p>
runtime/plugins/spark-battle-sim/spark-battle-sim.php:112:add_shortcode('spark_battle_sim', 'spark_battle_sim_shortcode');
```

## Spark Protocol class/function hits

```text
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Ambush.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Ambush.php:14:final class Ambush
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Ambush.php:21:    public static function modifier(Arena $arena): array
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Arena.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Arena.php:11:final class Arena
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Arena.php:33:    public function __construct(
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaConditions.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaConditions.php:13:final class ArenaConditions
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaConditions.php:45:    public static function derive(Arena $arena): array
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaConditions.php:88:        $has = static function (array $needles) use ($loc): bool {
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaConditions.php:150:    public static function modifierFor(Sheet $sheet, Arena $arena): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaRoller.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaRoller.php:14:final class ArenaRoller
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/ArenaRoller.php:45:    public static function roll(Rng $rng): Arena
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:14:final class BattleReport
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:27:    public function __construct(Arena $arena, Odds $odds, OutcomeResult $outcome, Sheet $a, Sheet $b)
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:36:    public function arena(): Arena
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:41:    public function odds(): Odds
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:46:    public function outcome(): OutcomeResult
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:51:    public function winnerName(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleReport.php:59:    public function showWork(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleSimulator.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleSimulator.php:23:final class BattleSimulator
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleSimulator.php:28:    public function __construct(?Rng $rng = null)
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/BattleSimulator.php:33:    public function simulate(Sheet $a, Sheet $b): BattleReport
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/EffectiveTier.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/EffectiveTier.php:19:final class EffectiveTier
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/EffectiveTier.php:26:    public static function onAxis(array $powers, string $axis): int
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/EffectiveTier.php:38:        usort($relevant, static function (Power $a, Power $b): int {
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/HardCounters.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/HardCounters.php:16:final class HardCounters
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/HardCounters.php:40:    public static function modifier(Sheet $a, Sheet $b): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/HardCounters.php:45:    private static function oneWay(Sheet $att, Sheet $def): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:12:final class Odds
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:23:    public function __construct(float $basePercent, float $arenaAdjustment, float $finalAPercent, string $reason)
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:31:    public function basePercent(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:36:    public function arenaAdjustment(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:41:    public function favoriteAPercent(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:46:    public function favoriteBPercent(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:51:    public function reason(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/Odds.php:60:    public function thresholdForA(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OddsAssessment.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OddsAssessment.php:17:final class OddsAssessment
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OddsAssessment.php:25:    public static function assess(Sheet $a, Sheet $b, Arena $arena): Odds
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OddsAssessment.php:62:    private static function baseFromCc(int $ccA, int $ccB): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OddsAssessment.php:68:    private static function clampPercent(float $p): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeLottery.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeLottery.php:17:final class OutcomeLottery
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeLottery.php:19:    public static function resolve(Odds $odds, string $nameA, string $nameB, Rng $rng): OutcomeResult
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:11:final class OutcomeResult
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:24:    public function __construct(
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:38:    public function winnerName(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:43:    public function roll(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:48:    public function thresholdForA(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/OutcomeResult.php:53:    public function fellInBand(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/StrategicTags.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/StrategicTags.php:14:final class StrategicTags
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/StrategicTags.php:29:    public static function modifier(Sheet $a, Sheet $b): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/StrategicTags.php:34:    private static function sumFor(Sheet $sheet): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php:5:namespace Spark\Battle;
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php:14:final class TierMatchup
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php:16:    public static function modifier(Sheet $a, Sheet $b): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php:41:    private static function offenseVsDefense(Sheet $att, Sheet $def): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php:55:    private static function axisGap(Sheet $a, Sheet $b, string $axis, float $big, float $small): float
_work/spark-protocol-inventory-008/spark-protocol/src/Battle/TierMatchup.php:65:    private static function gapToPoints(int $gap, float $big, float $small): float
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:5:namespace Spark\Engine;
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:16:final class CombatCapability
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:35:    private function __construct(
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:58:    public static function read(array $powers): self
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:128:    private static function fillSpine(array $powers): array
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:180:    private static function highestTierFloor(array $powers): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:193:    private static function roundHalfUp(float $n): float
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:198:    public function value(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:203:    public function center(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:208:    public function bench(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:213:    public function attackSlotValue(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:218:    public function defenseSlotValue(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:223:    public function attackPower(): ?Power
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:228:    public function defensePower(): ?Power
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/CombatCapability.php:233:    public function floorApplied(): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php:5:namespace Spark\Engine;
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php:12:final class DomainScores
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php:20:    public function __construct(array $scores)
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php:33:    public function get(string $domain): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php:42:    public function all(): array
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/DomainScores.php:47:    public function highest(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:5:namespace Spark\Engine;
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:19:final class SheetGenerator
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:25:    public static function scoreToTier(int $score): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:47:    public static function manifestedDomains(DomainScores $scores): array
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:68:    public static function sparkSignature(int $highestTier, int $secondTier, int $powerCount): int
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:75:    public static function cast(int $manifestedDomainCount): string
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:95:    public static function generate(string $maskName, DomainScores $scores, array $flavor): Sheet
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:138:    private static function powersForDomain(string $domain, array $vector): array
_work/spark-protocol-inventory-008/spark-protocol/src/Engine/SheetGenerator.php:170:    private static function alreadyHas(array $powers, string $name): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/CapacityCost.php:5:namespace Spark\Exchange;
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/CapacityCost.php:16:final class CapacityCost
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/CapacityCost.php:40:    public static function newPower(string $weight, int $tier): int
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/CapacityCost.php:46:    public static function advance(string $weight, int $targetTier): int
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/CapacityCost.php:52:    private static function assert(string $weight, int $tier, int $min): void
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php:5:namespace Spark\Exchange;
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php:22:final class Exchange
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php:27:    public function buyNewPower(Sheet $sheet, string $powerName, int $tier): RequisitionResult
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php:57:    public function upgradePower(Sheet $sheet, string $powerName, int $targetTier): RequisitionResult
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php:102:    private function guardCapacity(Sheet $sheet, int $cost, string $what): void
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/Exchange.php:120:    private function relock(Sheet $sheet, array $powers, int $cost, string $desc): RequisitionResult
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionRefused.php:5:namespace Spark\Exchange;
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionRefused.php:11:final class RequisitionRefused extends \RuntimeException
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php:5:namespace Spark\Exchange;
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php:10:final class RequisitionResult
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php:19:    public function __construct(Sheet $sheet, int $capacitySpent, string $description)
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php:26:    public function sheet(): Sheet
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php:31:    public function capacitySpent(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Exchange/RequisitionResult.php:36:    public function description(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/DomainRegistry.php:5:namespace Spark\Model;
_work/spark-protocol-inventory-008/spark-protocol/src/Model/DomainRegistry.php:12:final class DomainRegistry
_work/spark-protocol-inventory-008/spark-protocol/src/Model/DomainRegistry.php:30:    public static function isDomain(string $domain): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Model/DomainRegistry.php:38:    public static function powersOf(string $domain): array
_work/spark-protocol-inventory-008/spark-protocol/src/Model/DomainRegistry.php:46:    public static function containsPower(string $domain, string $power): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:5:namespace Spark\Model;
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:14:final class Power
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:28:    public function __construct(string $name, int $tier, ?string $healingExpression = null, ?string $densityMode = null)
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:42:    public function name(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:47:    public function tier(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:52:    public function weight(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:57:    public function role(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:62:    public function axis(): ?string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:67:    public function isStrategic(): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:72:    public function healingExpression(): ?string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:77:    public function densityMode(): ?string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:83:    public function spineValueRaw(): float
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:89:    public function benchValue(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Power.php:95:    public function withTier(int $tier): self
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:5:namespace Spark\Model;
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:11: *   - duel WEIGHT class (FULL / HALF / QUARTER) — Step 9 + Exchange
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:18:final class PowerRegistry
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:198:    public static function isKnown(string $power): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:203:    public static function getWeight(string $power): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:211:    public static function isStrategic(string $power): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:216:    public static function getRole(string $power): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:221:    public static function getAxis(string $power): ?string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:229:    public static function scaledMultiplier(string $power, int $tier): float
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:238:    public static function benchValue(string $power, int $tier): int
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:249:    public static function all(): array
_work/spark-protocol-inventory-008/spark-protocol/src/Model/PowerRegistry.php:254:    private static function assertTier(int $tier): void
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:5:namespace Spark\Model;
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:15:final class Sheet
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:35:    public function __construct(
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:57:    public function maskName(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:63:    public function powers(): array
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:68:    public function combatCapability(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:73:    public function sparkSignature(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:78:    public function isMaxed(): bool
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:84:    public function spareCapacity(): int
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:90:    public function threatClass(): string
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:96:    public function strategicTags(): array
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:107:    public function findPower(string $name): ?Power
_work/spark-protocol-inventory-008/spark-protocol/src/Model/Sheet.php:123:    public function relock(array $powers, int $combatCapability, bool $maxed = false): self
_work/spark-protocol-inventory-008/spark-protocol/src/Model/ThreatClass.php:5:namespace Spark\Model;
_work/spark-protocol-inventory-008/spark-protocol/src/Model/ThreatClass.php:10:final class ThreatClass
_work/spark-protocol-inventory-008/spark-protocol/src/Model/ThreatClass.php:21:    public static function fromCC(int $cc): string
_work/spark-protocol-inventory-008/spark-protocol/src/Support/MtRng.php:5:namespace Spark\Support;
_work/spark-protocol-inventory-008/spark-protocol/src/Support/MtRng.php:10:final class MtRng implements Rng
_work/spark-protocol-inventory-008/spark-protocol/src/Support/MtRng.php:12:    public function roll(int $max = 100): int
_work/spark-protocol-inventory-008/spark-protocol/src/Support/Rng.php:5:namespace Spark\Support;
_work/spark-protocol-inventory-008/spark-protocol/src/Support/Rng.php:16:    public function roll(int $max = 100): int;
_work/spark-protocol-inventory-008/spark-protocol/src/Support/SequenceRng.php:5:namespace Spark\Support;
_work/spark-protocol-inventory-008/spark-protocol/src/Support/SequenceRng.php:11:final class SequenceRng implements Rng
_work/spark-protocol-inventory-008/spark-protocol/src/Support/SequenceRng.php:21:    public function __construct(array $sequence)
_work/spark-protocol-inventory-008/spark-protocol/src/Support/SequenceRng.php:29:    public function roll(int $max = 100): int
```

## Recommended integration shape

1. Vendor Spark Protocol under `runtime/plugins/spark-battle-sim/includes/Spark` for a WordPress-plugin-safe self-contained package.
2. Add `runtime/plugins/spark-battle-sim/includes/SparkProtocolAdapter.php`.
3. Adapter translates plugin fighter data into `Spark\Model\Sheet` objects.
4. Adapter calls `Spark\Battle\BattleSimulator`.
5. Existing shortcode/UI gets player-safe winner + narrative summary.
6. Operator/debug path may expose `showWork()`.

## Verification gate for next lane

- Add deterministic adapter smoke script.
- Confirm two fighters resolve through Spark Protocol.
- Confirm player output hides raw odds.
- Confirm operator output includes arena, odds, roll, winner.

Status: PASS
