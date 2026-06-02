<?php

declare(strict_types=1);

/**
 * Spark Protocol demo. Run: php bin/demo.php
 *
 * Walks the full deterministic pipeline and prints an operator-style
 * SHOWWORK trace. Uses a fixed RNG seed-sequence so the output is stable;
 * swap in MtRng for real randomness.
 */

require __DIR__ . '/../autoload.php';

use Spark\Battle\BattleSimulator;
use Spark\Engine\CombatCapability;
use Spark\Engine\DomainScores;
use Spark\Engine\SheetGenerator;
use Spark\Exchange\Exchange;
use Spark\Exchange\RequisitionRefused;
use Spark\Support\MtRng;

function line(string $s = ''): void
{
    echo $s . "\n";
}

line('=== SPARK PROTOCOL — DETERMINISTIC ENGINE DEMO ===');
line();

// 1) Generate two sheets from scored domains + flavor vectors.
$capScores = new DomainScores([
    'Titan' => 22, 'Mind' => 14, 'Velocity' => 8,
    'Energy' => 4, 'Specter' => 4, 'Duality' => 4, 'Omni' => 4, 'Primal' => 0,
]);
$cap = SheetGenerator::generate('Cap Wilson', $capScores, [
    'Titan'    => ['Super Strength' => 5, 'Invulnerability' => 4],
    'Mind'     => ['Psychic Defense' => 3, 'Telepathy' => 2],
    'Velocity' => ['Flight' => 2],
]);

$victorScores = new DomainScores([
    'Velocity' => 22, 'Titan' => 16, 'Energy' => 5,
    'Specter' => 5, 'Duality' => 5, 'Omni' => 0, 'Primal' => 0, 'Mind' => 0,
]);
$victor = SheetGenerator::generate('The Victor', $victorScores, [
    'Velocity' => ['Super Speed' => 4, 'Flight' => 3],
    'Titan'    => ['Unstoppable Momentum' => 4, 'Invulnerability' => 2],
]);

foreach ([$cap, $victor] as $sheet) {
    $cc = CombatCapability::read($sheet->powers());
    line(sprintf(
        '%-12s  CC %3d  %-6s  Signature %d  spare %d',
        $sheet->maskName(),
        $sheet->combatCapability(),
        $sheet->threatClass(),
        $sheet->sparkSignature(),
        $sheet->spareCapacity()
    ));
    foreach ($sheet->powers() as $p) {
        line(sprintf('    %-22s T%d  %-7s%s', $p->name(), $p->tier(), $p->weight(),
            $p->isStrategic() ? '  [strategic]' : ''));
    }
    $tags = $sheet->strategicTags();
    if ($tags !== []) {
        line('    AEGIS designation: Strategic Threat (' . implode(', ', $tags) . ')');
    }
    line();
}

// 2) Exchange: try to requisition a new power for Cap.
line('--- THE EXCHANGE ---');
$ex = new Exchange();
try {
    $result = $ex->buyNewPower($cap, 'Concussive Blasts', 3);
    line($result->description());
    line(sprintf(
        '  %s: CC %d -> %d (%s)%s',
        $result->sheet()->maskName(),
        $cap->combatCapability(),
        $result->sheet()->combatCapability(),
        $result->sheet()->threatClass(),
        $result->sheet()->isMaxed() ? '  MAXED' : ''
    ));
    $cap = $result->sheet();
} catch (RequisitionRefused $e) {
    line('  Refused: ' . $e->getMessage());
}
line();

// 3) Simulate a fight (real RNG: arena roll, then a separate outcome roll).
line('--- BATTLE SIMULATION (operator SHOWWORK) ---');
$sim = new BattleSimulator(new MtRng());
$report = $sim->simulate($cap, $victor);
line($report->showWork());
line();
line('(Players would receive only the narrative — never this block.)');
