<?php

declare(strict_types=1);

namespace Spark\Tests\Integration;

use Spark\Battle\BattleSimulator;
use Spark\Engine\DomainScores;
use Spark\Engine\SheetGenerator;
use Spark\Exchange\Exchange;
use Spark\Support\SequenceRng;
use Spark\Tests\TestCase;

/**
 * End-to-end: generate two sheets, run one through the Exchange, then
 * simulate a deterministic fight. Verifies the subsystems compose.
 */
final class FullPipelineTest extends TestCase
{
    public function testGenerateExchangeSimulate(): void
    {
        // Brawler: solo Titan T4.
        $brawlerScores = new DomainScores([
            'Titan' => 22, 'Velocity' => 5, 'Energy' => 5, 'Specter' => 5,
            'Duality' => 5, 'Omni' => 0, 'Primal' => 0, 'Mind' => 0,
        ]);
        $brawler = SheetGenerator::generate('Bulwark', $brawlerScores, [
            'Titan' => ['Super Strength' => 5, 'Invulnerability' => 3],
        ]);

        // Striker: Energy T4 pyrokinetic.
        $strikerScores = new DomainScores([
            'Energy' => 22, 'Titan' => 5, 'Velocity' => 5, 'Specter' => 5,
            'Duality' => 5, 'Omni' => 0, 'Primal' => 0, 'Mind' => 0,
        ]);
        $striker = SheetGenerator::generate('Ember', $strikerScores, [
            'Energy' => ['Pyrokinesis' => 5, 'Concussive Blasts' => 3],
        ]);

        // Bulwark has attack + defense (SS + Invuln) -> CC 80, Sigma.
        // Ember is a defenseless glass cannon (two attackers, no defense)
        // -> the empty defense slot caps it at CC 44, Gamma. This is the
        // protocol behaving correctly, not a bug: no defense reads low.
        $this->assertSame(80, $brawler->combatCapability());
        $this->assertSame('Sigma', $brawler->threatClass());
        $this->assertSame(44, $striker->combatCapability());
        $this->assertSame('Gamma', $striker->threatClass());

        // Exchange: buy the brawler a new half-weight power if affordable.
        $ex = new Exchange();
        $spare = $brawler->spareCapacity();
        if ($spare >= 1) {
            $result = $ex->buyNewPower($brawler, 'Flight', 1); // HALF T1 = 1
            $brawler = $result->sheet();
            $this->assertTrue($brawler->findPower('Flight') !== null);
        }

        // Simulate with a deterministic RNG (arena rolls + one outcome roll).
        // Sequence: 7 arena rolls then 1 outcome roll. Values clamp per-die.
        $rng = new SequenceRng([12, 4, 1, 4, 1, 1, 2, /* outcome */ 1]);
        $sim = new BattleSimulator($rng);
        $report = $sim->simulate($brawler, $striker);

        // A low outcome roll (1) lands in fighter A's band -> Bulwark wins.
        $this->assertSame('Bulwark', $report->winnerName());

        // SHOWWORK renders without leaking exceptions.
        $work = $report->showWork();
        $this->assertTrue(strpos($work, 'Winner: Bulwark') !== false);
    }

    public function testMultipleRunsConvergeToOdds(): void
    {
        $brawlerScores = new DomainScores([
            'Titan' => 28, 'Velocity' => 5, 'Energy' => 5, 'Specter' => 5,
            'Duality' => 5, 'Omni' => 1, 'Primal' => 1, 'Mind' => 0,
        ]);
        $strong = SheetGenerator::generate('Titan', $brawlerScores, [
            'Titan' => ['Super Strength' => 5, 'Invulnerability' => 4],
        ]);
        $weakScores = new DomainScores([
            'Velocity' => 13, 'Titan' => 3, 'Energy' => 3, 'Specter' => 3,
            'Duality' => 3, 'Omni' => 0, 'Primal' => 0, 'Mind' => 0,
        ]);
        $weak = SheetGenerator::generate('Dash', $weakScores, [
            'Velocity' => ['Super Speed' => 4],
        ]);

        // Fixed neutral-ish arena via repeated mid rolls; outcome swept 1..100.
        // We only check that the strong fighter wins the large majority.
        $seq = [];
        for ($i = 0; $i < 100; $i++) {
            // 7 arena rolls (mid values) + 1 outcome roll sweeping 1..100
            array_push($seq, 12, 4, 2, 4, 1, 1, 2, ($i % 100) + 1);
        }
        $rng = new SequenceRng($seq);
        $sim = new BattleSimulator($rng);

        $strongWins = 0;
        for ($i = 0; $i < 100; $i++) {
            if ($sim->simulate($strong, $weak)->winnerName() === 'Titan') {
                $strongWins++;
            }
        }
        // Overwhelming gap: strong fighter should win the clear majority.
        $this->assertGreaterThanOrEqual(60, $strongWins);
    }
}
