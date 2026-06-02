<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Battle\Arena;
use Spark\Battle\OddsAssessment;
use Spark\Battle\OutcomeLottery;
use Spark\Model\Power;
use Spark\Model\Sheet;
use Spark\Support\SequenceRng;
use Spark\Tests\TestCase;

final class OutcomeLotteryTest extends TestCase
{
    private function odds()
    {
        $a = new Sheet('A', [new Power('Super Strength', 5)], 80);
        $b = new Sheet('B', [new Power('Flight', 3)], 60);
        $arena = new Arena('Stadium field', 'Afternoon', 'Clear and dry', 'Mild',
            Arena::POS_OPEN, Arena::POS_OPEN, 'Mid');
        return OddsAssessment::assess($a, $b, $arena);
    }

    /** A roll at or below the threshold hands A the win. */
    public function testRollInFavoriteBandWinsForA(): void
    {
        $odds = $this->odds();
        $rng = new SequenceRng([1]); // lowest possible roll
        $result = OutcomeLottery::resolve($odds, 'A', 'B', $rng);
        $this->assertSame('A', $result->winnerName());
    }

    /** A roll above the threshold hands B the win. */
    public function testRollInUnderdogBandWinsForB(): void
    {
        $odds = $this->odds();
        $rng = new SequenceRng([100]); // highest possible roll
        $result = OutcomeLottery::resolve($odds, 'A', 'B', $rng);
        $this->assertSame('B', $result->winnerName());
    }

    /** The committed threshold is recorded and matches the odds. */
    public function testThresholdRecorded(): void
    {
        $odds = $this->odds();
        $rng = new SequenceRng([50]);
        $result = OutcomeLottery::resolve($odds, 'A', 'B', $rng);
        $this->assertSame($odds->thresholdForA(), $result->thresholdForA());
    }

    /** The roll value is recorded for the SHOWWORK block. */
    public function testRollRecorded(): void
    {
        $odds = $this->odds();
        $rng = new SequenceRng([42]);
        $result = OutcomeLottery::resolve($odds, 'A', 'B', $rng);
        $this->assertSame(42, $result->roll());
    }

    /** Over many runs the win rate converges to the assessed odds. */
    public function testConvergesToOdds(): void
    {
        $odds = $this->odds();
        $threshold = $odds->thresholdForA();
        // Deterministic uniform sweep 1..100 repeated: exactly threshold% to A.
        $seq = [];
        for ($i = 0; $i < 1000; $i++) {
            $seq[] = ($i % 100) + 1;
        }
        $rng = new SequenceRng($seq);
        $aWins = 0;
        for ($i = 0; $i < 1000; $i++) {
            if (OutcomeLottery::resolve($odds, 'A', 'B', $rng)->winnerName() === 'A') {
                $aWins++;
            }
        }
        // Expected A win share = threshold% exactly under the uniform sweep.
        $this->assertSame($threshold * 10, $aWins);
    }

    /** Boundary: a roll exactly equal to the threshold is a favorite win. */
    public function testBoundaryRollIsFavorite(): void
    {
        $odds = $this->odds();
        $rng = new SequenceRng([$odds->thresholdForA()]);
        $result = OutcomeLottery::resolve($odds, 'A', 'B', $rng);
        $this->assertSame('A', $result->winnerName());
    }
}
