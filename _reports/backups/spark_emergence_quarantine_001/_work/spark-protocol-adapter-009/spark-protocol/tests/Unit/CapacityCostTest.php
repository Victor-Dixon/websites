<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Exchange\CapacityCost;
use Spark\Tests\TestCase;

/**
 * The Exchange capacity cost table (FIXED). Assertions transcribe the
 * cost table and the two canonical anchors verbatim.
 */
final class CapacityCostTest extends TestCase
{
    /** FULL-weight new-power column: 2 / 5 / 9 / 18 / 30. */
    public function testFullNewPowerColumn(): void
    {
        $this->assertSame(2, CapacityCost::newPower('FULL', 1));
        $this->assertSame(5, CapacityCost::newPower('FULL', 2));
        $this->assertSame(9, CapacityCost::newPower('FULL', 3));
        $this->assertSame(18, CapacityCost::newPower('FULL', 4));
        $this->assertSame(30, CapacityCost::newPower('FULL', 5));
    }

    /** FULL-weight tier-advancement column: 3 / 4 / 9 / 12 (to T2..T5). */
    public function testFullAdvanceColumn(): void
    {
        $this->assertSame(3, CapacityCost::advance('FULL', 2));
        $this->assertSame(4, CapacityCost::advance('FULL', 3));
        $this->assertSame(9, CapacityCost::advance('FULL', 4));
        $this->assertSame(12, CapacityCost::advance('FULL', 5));
    }

    /** HALF new-power column: 1 / 3 / 5 / 11 / 18. */
    public function testHalfNewPowerColumn(): void
    {
        $this->assertSame(1, CapacityCost::newPower('HALF', 1));
        $this->assertSame(3, CapacityCost::newPower('HALF', 2));
        $this->assertSame(5, CapacityCost::newPower('HALF', 3));
        $this->assertSame(11, CapacityCost::newPower('HALF', 4));
        $this->assertSame(18, CapacityCost::newPower('HALF', 5));
    }

    /** HALF tier-advancement: 2 / 2 / 6 / 7. */
    public function testHalfAdvanceColumn(): void
    {
        $this->assertSame(2, CapacityCost::advance('HALF', 2));
        $this->assertSame(2, CapacityCost::advance('HALF', 3));
        $this->assertSame(6, CapacityCost::advance('HALF', 4));
        $this->assertSame(7, CapacityCost::advance('HALF', 5));
    }

    /** QUARTER new-power column: 1 / 2 / 3 / 6 / 11. */
    public function testQuarterNewPowerColumn(): void
    {
        $this->assertSame(1, CapacityCost::newPower('QUARTER', 1));
        $this->assertSame(2, CapacityCost::newPower('QUARTER', 2));
        $this->assertSame(3, CapacityCost::newPower('QUARTER', 3));
        $this->assertSame(6, CapacityCost::newPower('QUARTER', 4));
        $this->assertSame(11, CapacityCost::newPower('QUARTER', 5));
    }

    /** QUARTER tier-advancement: 1 / 1 / 3 / 5. */
    public function testQuarterAdvanceColumn(): void
    {
        $this->assertSame(1, CapacityCost::advance('QUARTER', 2));
        $this->assertSame(1, CapacityCost::advance('QUARTER', 3));
        $this->assertSame(3, CapacityCost::advance('QUARTER', 4));
        $this->assertSame(5, CapacityCost::advance('QUARTER', 5));
    }

    /** Anchor 1: a new T3 (9) and a FULL ->T4 (9) both cost 9. */
    public function testAnchor1BothBuysCostNine(): void
    {
        $this->assertSame(9, CapacityCost::newPower('FULL', 3));
        $this->assertSame(9, CapacityCost::advance('FULL', 4));
    }

    /** Equal paths to the ceiling: each of these loadouts sums to 90. */
    public function testEqualPathsAllSumToNinety(): void
    {
        $t5 = CapacityCost::newPower('FULL', 5);
        $t4 = CapacityCost::newPower('FULL', 4);
        $t3 = CapacityCost::newPower('FULL', 3);

        $this->assertSame(90, 3 * $t5);
        $this->assertSame(90, 4 * $t4 + 2 * $t3);
        $this->assertSame(90, 3 * $t4 + 4 * $t3);
        $this->assertSame(90, 6 * $t3 + 2 * $t4);
    }

    /** New-power column is monotonic with steps +3/+4/+9/+12. */
    public function testFullColumnMonotonicSteps(): void
    {
        $col = [
            CapacityCost::newPower('FULL', 1),
            CapacityCost::newPower('FULL', 2),
            CapacityCost::newPower('FULL', 3),
            CapacityCost::newPower('FULL', 4),
            CapacityCost::newPower('FULL', 5),
        ];
        $this->assertSame(3, $col[1] - $col[0]);
        $this->assertSame(4, $col[2] - $col[1]);
        $this->assertSame(9, $col[3] - $col[2]);
        $this->assertSame(12, $col[4] - $col[3]);
    }

    /** Advance cell equals the gap between adjacent new-power cells (FULL ->T4). */
    public function testAdvanceEqualsNewPowerGap(): void
    {
        $gap = CapacityCost::newPower('FULL', 4) - CapacityCost::newPower('FULL', 3);
        $this->assertSame($gap, CapacityCost::advance('FULL', 4));
    }
}
