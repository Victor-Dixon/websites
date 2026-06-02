<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;

/**
 * Tier-matchup modifier (Battle_Sim Step 1). Uses EFFECTIVE tiers
 * (post-compounding) on each contested axis, then converts the gap into
 * percentage-point swings toward A. Symmetric: assess(A,B) = -assess(B,A).
 */
final class TierMatchup
{
    public static function modifier(Sheet $a, Sheet $b): float
    {
        $mod = 0.0;

        // A's offense vs B's defense, and vice-versa (physical + energy).
        $mod += self::offenseVsDefense($a, $b);
        $mod -= self::offenseVsDefense($b, $a);

        // Mental axis (offense vs the same mental axis on defense).
        $mod += self::axisGap($a, $b, 'mental', 6.0, 3.0);

        // Mobility: who controls range.
        $mod += self::axisGap($a, $b, 'mobility', 5.0, 2.5);

        // Stealth vs perception (flat, smaller).
        $sa = EffectiveTier::onAxis($a->powers(), 'stealth');
        $pb = EffectiveTier::onAxis($b->powers(), 'perception');
        if ($sa > 0 && $pb > 0) {
            $mod += ($sa <=> $pb) * 3.0;
        }

        return $mod;
    }

    /** A's phys/energy offense against B's physical defense. */
    private static function offenseVsDefense(Sheet $att, Sheet $def): float
    {
        $mod = 0.0;
        $tDef = EffectiveTier::onAxis($def->powers(), 'phys_def');
        foreach (['phys_off', 'eng_off'] as $offAxis) {
            $tOff = EffectiveTier::onAxis($att->powers(), $offAxis);
            if ($tOff > 0 && $tDef > 0) {
                $mod += self::gapToPoints($tOff - $tDef, 8.0, 4.0);
            }
        }
        return $mod;
    }

    /** Symmetric gap on a shared axis (A's axis vs B's axis). */
    private static function axisGap(Sheet $a, Sheet $b, string $axis, float $big, float $small): float
    {
        $ta = EffectiveTier::onAxis($a->powers(), $axis);
        $tb = EffectiveTier::onAxis($b->powers(), $axis);
        if ($ta === 0 && $tb === 0) {
            return 0.0;
        }
        return self::gapToPoints($ta - $tb, $big, $small);
    }

    private static function gapToPoints(int $gap, float $big, float $small): float
    {
        if ($gap >= 2) {
            return $big;
        }
        if ($gap === 1) {
            return $small;
        }
        if ($gap === -1) {
            return -$small;
        }
        if ($gap <= -2) {
            return -$big;
        }
        return 0.0;
    }
}
