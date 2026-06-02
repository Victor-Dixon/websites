<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Battle\EffectiveTier;
use Spark\Model\Power;
use Spark\Tests\TestCase;

/**
 * Compounding magnification (Battle_Sim Step 1): anchor on the highest
 * power on an axis, +1 per additional T3+ reinforcer, ceiling T6.
 */
final class EffectiveTierTest extends TestCase
{
    /** A standing T2 punch alone is a T2 hit. */
    public function testSinglePowerIsItsTier(): void
    {
        $t = EffectiveTier::onAxis([new Power('Super Strength', 2)], 'phys_off');
        $this->assertSame(2, $t);
    }

    /** Speed + Momentum + Strength into one charge: anchor + reinforcers. */
    public function testChargeCompounds(): void
    {
        // phys_off powers: Super Strength T4, Unstoppable Momentum T3 (+1).
        // (Super Speed is mobility, not phys_off, so it doesn't add here.)
        $powers = [
            new Power('Super Strength', 4),
            new Power('Unstoppable Momentum', 3),
        ];
        $this->assertSame(5, EffectiveTier::onAxis($powers, 'phys_off'));
    }

    /** T1-T2 reinforcers add nothing; only T3+ contributes +1. */
    public function testLowTierReinforcersAddNothing(): void
    {
        $powers = [
            new Power('Super Strength', 4),
            new Power('Vibration Control', 2), // T2 -> no +1
        ];
        $this->assertSame(4, EffectiveTier::onAxis($powers, 'phys_off'));
    }

    /** Ceiling is T6, reachable only by combination. */
    public function testCeilingIsSix(): void
    {
        $powers = [
            new Power('Invulnerability', 5),
            new Power('Density Control', 3), // +1 -> 6
            new Power('Force Fields', 4),     // would be +1 more but capped at 6
        ];
        $this->assertSame(6, EffectiveTier::onAxis($powers, 'phys_def'));
    }

    /** No powers on the axis -> tier 0. */
    public function testNoPowersOnAxis(): void
    {
        $t = EffectiveTier::onAxis([new Power('Flight', 3)], 'phys_off');
        $this->assertSame(0, $t);
    }

    /** T5 anchor + one T3 reinforcer already hits the T6 ceiling. */
    public function testT5PlusOneReinforcerHitsCeiling(): void
    {
        $powers = [
            new Power('Super Strength', 5),
            new Power('Kinetic Manipulation', 3),
        ];
        $this->assertSame(6, EffectiveTier::onAxis($powers, 'phys_off'));
    }
}
