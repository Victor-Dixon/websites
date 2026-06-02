<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/spark-protocol-autoload.php';

use Spark\Battle\BattleSimulator;
use Spark\Engine\CombatCapability;
use Spark\Model\Power;
use Spark\Model\Sheet;
use Spark\Support\SequenceRng;

function sheet_from_powers(string $name, array $powers): Sheet
{
    $powerObjects = [];
    foreach ($powers as $power) {
        $powerObjects[] = new Power($power[0], $power[1]);
    }

    $cc = CombatCapability::read($powerObjects)->value();

    return new Sheet($name, $powerObjects, $cc);
}

$capWilson = sheet_from_powers('Cap Wilson', [
    ['Super Strength', 4],
    ['Invulnerability', 4],
    ['Flight', 2],
    ['Psychic Defense', 3],
    ['Telepathy', 3],
]);

$theVictor = sheet_from_powers('The Victor', [
    ['Unstoppable Momentum', 3],
    ['Invulnerability', 3],
    ['Super Speed', 4],
    ['Flight', 4],
]);

// SequenceRng pins arena + outcome so the smoke is deterministic.
// This is proof-only. Production should use MtRng/random_int.
$rng = new SequenceRng([
    1,  // arena location
    2,  // time
    3,  // weather
    4,  // temperature
    5,  // distance
    87, // outcome roll
]);

$report = (new BattleSimulator($rng))->simulate($capWilson, $theVictor);

echo "SPARK_PROTOCOL_ADAPTER_SMOKE=PASS\n";
echo "A={$capWilson->maskName()} CC={$capWilson->combatCapability()}\n";
echo "B={$theVictor->maskName()} CC={$theVictor->combatCapability()}\n";
echo "WINNER={$report->winnerName()}\n";
echo "SHOWWORK_BEGIN\n";
echo $report->showWork() . "\n";
echo "SHOWWORK_END\n";
