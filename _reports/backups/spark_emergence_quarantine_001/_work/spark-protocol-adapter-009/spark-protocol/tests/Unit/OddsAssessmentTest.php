<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Battle\Arena;
use Spark\Battle\OddsAssessment;
use Spark\Model\Power;
use Spark\Model\Sheet;
use Spark\Tests\TestCase;

final class OddsAssessmentTest extends TestCase
{
    private function neutralArena(): Arena
    {
        return new Arena('Stadium field', 'Afternoon', 'Clear and dry', 'Mild',
            Arena::POS_OPEN, Arena::POS_OPEN, 'Mid');
    }

    private function sheet(string $name, int $cc, array $powers): Sheet
    {
        return new Sheet($name, $powers, $cc);
    }

    /** Equal CC and identical kits in a neutral arena -> 50/50. */
    public function testEvenMatchIsFifty(): void
    {
        $a = $this->sheet('A', 60, [new Power('Super Strength', 4)]);
        $b = $this->sheet('B', 60, [new Power('Super Strength', 4)]);
        $odds = OddsAssessment::assess($a, $b, $this->neutralArena());
        $this->assertEqualsWithDelta(50.0, $odds->favoriteAPercent(), 0.001);
    }

    /** Bigger CC favours A; probability rises monotonically with the gap. */
    public function testHigherCcFavoured(): void
    {
        $arena = $this->neutralArena();
        $small = OddsAssessment::assess(
            $this->sheet('A', 70, [new Power('Super Strength', 4)]),
            $this->sheet('B', 60, [new Power('Super Strength', 4)]),
            $arena
        )->favoriteAPercent();
        $big = OddsAssessment::assess(
            $this->sheet('A', 90, [new Power('Super Strength', 4)]),
            $this->sheet('B', 60, [new Power('Super Strength', 4)]),
            $arena
        )->favoriteAPercent();
        $this->assertGreaterThanOrEqual(50.0, $small);
        $this->assertGreaterThanOrEqual($small, $big);
    }

    /** The arena swing never exceeds +-25 from the base. */
    public function testArenaSwingCappedAt25(): void
    {
        // Pyrokinetic favourite dropped into a storm over standing water.
        $a = $this->sheet('Pyro', 80, [new Power('Pyrokinesis', 5)]);
        $b = $this->sheet('Brute', 50, [new Power('Super Strength', 4)]);
        $hostile = new Arena('City docks', 'Night', 'Heavy rain / storm', 'Cold',
            Arena::POS_OPEN, Arena::POS_OPEN, 'Long');
        $odds = OddsAssessment::assess($a, $b, $hostile);
        $base = $odds->basePercent();
        $final = $odds->favoriteAPercent();
        $this->assertLessThanOrEqual(25.0 + 0.001, abs($base - $final));
    }

    /** A 90/10 base shaved by the full -25 still sits at ~65, never flips. */
    public function testWideGapCannotInvert(): void
    {
        $a = $this->sheet('Monster', 100, [new Power('Pyrokinesis', 5)]);
        $b = $this->sheet('Mortal', 10, [new Power('Super Strength', 2)]);
        $hostile = new Arena('Frozen reservoir (seasonal)', 'Night', 'Heavy rain / storm', 'Freezing',
            Arena::POS_OPEN, Arena::POS_OPEN, 'Maximum');
        $final = OddsAssessment::assess($a, $b, $hostile)->favoriteAPercent();
        $this->assertGreaterThanOrEqual(50.0, $final, 'a 90/10 gap must not invert');
    }

    /** Probabilities are complementary and within [1, 99]. */
    public function testProbabilitiesComplementary(): void
    {
        $a = $this->sheet('A', 85, [new Power('Super Strength', 5)]);
        $b = $this->sheet('B', 55, [new Power('Flight', 3)]);
        $odds = OddsAssessment::assess($a, $b, $this->neutralArena());
        $this->assertEqualsWithDelta(
            100.0,
            $odds->favoriteAPercent() + $odds->favoriteBPercent(),
            0.001
        );
        $this->assertGreaterThanOrEqual(1.0, $odds->favoriteAPercent());
        $this->assertLessThanOrEqual(99.0, $odds->favoriteAPercent());
    }

    /** Swapping A and B mirrors the odds (no positional bias). */
    public function testSymmetryUnderSwap(): void
    {
        $arena = $this->neutralArena();
        $a = $this->sheet('A', 80, [new Power('Super Strength', 5)]);
        $b = $this->sheet('B', 60, [new Power('Flight', 4)]);
        $ab = OddsAssessment::assess($a, $b, $arena)->favoriteAPercent();
        $ba = OddsAssessment::assess($b, $a, $arena)->favoriteBPercent();
        $this->assertEqualsWithDelta($ab, $ba, 0.001);
    }

    /** Ambush requires concealment + low visibility + an exposed opponent. */
    public function testAmbushPushesTowardConcealedFighter(): void
    {
        $a = $this->sheet('A', 60, [new Power('Super Strength', 4)]);
        $b = $this->sheet('B', 60, [new Power('Super Strength', 4)]);
        $ambush = new Arena('City docks', 'Night', 'Fog', 'Cold',
            Arena::POS_BELOW, Arena::POS_OPEN, 'Mid');
        $odds = OddsAssessment::assess($a, $b, $ambush);
        $this->assertGreaterThanOrEqual(50.0, $odds->favoriteAPercent());
    }
}
