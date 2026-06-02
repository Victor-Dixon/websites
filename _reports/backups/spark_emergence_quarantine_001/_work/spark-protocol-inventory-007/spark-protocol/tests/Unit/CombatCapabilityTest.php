<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Engine\CombatCapability;
use Spark\Model\Power;
use Spark\Tests\TestCase;

/**
 * Step 9 (Spine -> Center -> Bench -> Cap). Every assertion below is a
 * WORKED READ transcribed verbatim from Protocol v8.5, so the engine is
 * pinned to the rulebook, not to my interpretation of it.
 */
final class CombatCapabilityTest extends TestCase
{
    /** Anchor 2: Super Strength T5 + Invulnerability T5 co-leads -> CC 100. */
    public function testAnchor2DualT5CoLeads(): void
    {
        $cc = CombatCapability::read([
            new Power('Super Strength', 5),
            new Power('Invulnerability', 5),
        ]);
        $this->assertSame(100, $cc->value());
        $this->assertSame(5.0, $cc->attackSlotValue());
        $this->assertSame(5.0, $cc->defenseSlotValue());
        $this->assertSame(0, $cc->bench());
    }

    /** Anchor 1: Pyro T5 + Invuln T4 + one HALF T1 bench -> CC 91. */
    public function testAnchor1NinetyOne(): void
    {
        $cc = CombatCapability::read([
            new Power('Pyrokinesis', 5),     // attack 5x1 = 5
            new Power('Invulnerability', 4), // defense 4x1 = 4 -> spine 9, center 90
            new Power('Flight', 1),          // HALF T1 bench = 1
        ]);
        $this->assertSame(91, $cc->value());
        $this->assertSame(90, $cc->center());
        $this->assertSame(1, $cc->bench());
    }

    /** Lone heavy: Super Strength T5, no defense -> empty slot, CC 50. */
    public function testLoneHeavyEmptyDefenseSlot(): void
    {
        $cc = CombatCapability::read([new Power('Super Strength', 5)]);
        $this->assertSame(50, $cc->value());
        $this->assertSame(0.0, $cc->defenseSlotValue());
    }

    /** Scaled indirect arbiter -> CC 77 (was 66 under old flat weights). */
    public function testScaledIndirectArbiter(): void
    {
        $cc = CombatCapability::read([
            new Power('Kinetic Manipulation', 4), // attack 4x1 = 4
            new Power('Force Fields', 4),         // defense 4x0.75 = 3 -> spine 7, center 70
            new Power('Energy Absorption', 4),    // bench HALF T4 = 3
            new Power('Psychic Defense', 3),      // bench HALF T3 = 2
            new Power('Danger Sense', 3),         // bench HALF T3 = 2  -> bench 7
        ]);
        $this->assertSame(77, $cc->value());
        $this->assertSame(70, $cc->center());
        $this->assertSame(7, $cc->bench());
    }

    /** Lone T5 indirect: Gravity Control T5 HALF -> floor lifts to CC 50. */
    public function testLoneT5IndirectFloor(): void
    {
        $cc = CombatCapability::read([new Power('Gravity Control', 5)]);
        $this->assertSame(50, $cc->value());
    }

    /** Force Fields T4 as sole defense fills slot at 4x0.75 = 3.0, not 0. */
    public function testHalfWeightFillsEmptySlotScaled(): void
    {
        $cc = CombatCapability::read([
            new Power('Concussive Blasts', 4), // attack 4
            new Power('Force Fields', 4),      // defense 3 -> spine 7 -> 70
        ]);
        $this->assertSame(70, $cc->value());
    }

    /** Solo T5 Telepath (QUARTER) floors at 5x0.75x10 = 37.5 -> 38. */
    public function testSoloTelepathFloorRounding(): void
    {
        $cc = CombatCapability::read([new Power('Telepathy', 5)]);
        $this->assertSame(38, $cc->value());
    }

    /** A co-lead that fills a slot must NOT also bench (counted once). */
    public function testCoLeadNotDoubleCounted(): void
    {
        $cc = CombatCapability::read([
            new Power('Super Strength', 5),
            new Power('Invulnerability', 5),
        ]);
        // If Invuln were also benched we'd exceed 100 pre-cap; bench must be 0.
        $this->assertSame(0, $cc->bench());
    }

    /** CC is capped at 100. */
    public function testCapAtHundred(): void
    {
        $cc = CombatCapability::read([
            new Power('Super Strength', 5),
            new Power('Invulnerability', 5),
            new Power('Pyrokinesis', 5), // would bench +5 but cap holds
        ]);
        $this->assertSame(100, $cc->value());
    }

    /** Empty power set reads 0 (Alpha). */
    public function testNoPowersReadsZero(): void
    {
        $cc = CombatCapability::read([]);
        $this->assertSame(0, $cc->value());
    }
}
