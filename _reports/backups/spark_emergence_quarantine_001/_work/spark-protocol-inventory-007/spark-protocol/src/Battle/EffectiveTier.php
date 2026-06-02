<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Power;

/**
 * Compounding magnification (Battle_Sim Step 1, "STACKING").
 *
 * Effective tier on an axis = highest single power's tier, +1 for EACH
 * additional power on that axis that is itself T3 or higher, capped at T6.
 * T1-T2 powers never contribute a +1.
 *
 * Arena-gating ("a power only joins when the arena allows it") is applied
 * by the caller, which filters the power list before calling this.
 */
final class EffectiveTier
{
    public const CEILING = 6;

    /**
     * @param Power[] $powers
     */
    public static function onAxis(array $powers, string $axis): int
    {
        $relevant = [];
        foreach ($powers as $p) {
            if ($p->axis() === $axis) {
                $relevant[] = $p;
            }
        }
        if (count($relevant) === 0) {
            return 0;
        }

        usort($relevant, static function (Power $a, Power $b): int {
            return $b->tier() <=> $a->tier();
        });

        $anchor = $relevant[0]->tier();
        $reinforcers = 0;
        for ($i = 1; $i < count($relevant); $i++) {
            if ($relevant[$i]->tier() >= 3) {
                $reinforcers++;
            }
        }

        return min($anchor + $reinforcers, self::CEILING);
    }
}
