<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;

/**
 * Step 1 odds assessment. Deterministic given the sheets and the rolled
 * arena. Starts from the CC gap (the primary lever), layers tier-matchup,
 * hard-counter, strategic-tag and arena/ambush adjustments, then clamps
 * the *total situational swing* to +-25 from the base so terrain can
 * drag a fight toward even — even invert a narrow gap — but never erase a
 * wide one (a 90/10 shaved by the full -25 still sits at 65/35).
 */
final class OddsAssessment
{
    /** Sigmoid steepness on the CC gap. */
    private const STEEPNESS = 0.06;

    /** The one hard limit: arena/situational swing ceiling, in points. */
    private const SWING_CAP = 25.0;

    public static function assess(Sheet $a, Sheet $b, Arena $arena): Odds
    {
        $base = self::baseFromCc($a->combatCapability(), $b->combatCapability());

        // Sheet-only adjustments (tier matchups, hard counters, strategic tags)
        // belong to the BASE per the rulebook ("the split the sheets alone
        // justify"). Arena + ambush are the situational swing that gets capped.
        $sheetAdj = TierMatchup::modifier($a, $b)
            + HardCounters::modifier($a, $b)
            + StrategicTags::modifier($a, $b);

        $base = self::clampPercent($base + $sheetAdj);

        $arenaDelta = ArenaConditions::modifierFor($a, $arena)
            - ArenaConditions::modifierFor($b, $arena);
        [$ambush, $ambushSide] = Ambush::modifier($arena);
        $situational = $arenaDelta + $ambush;

        // Cap the situational swing to +-25 from the base.
        $situational = max(-self::SWING_CAP, min(self::SWING_CAP, $situational));

        $final = self::clampPercent($base + $situational);

        $reason = sprintf(
            'base %d/%d; arena %+0.0f%s -> %d/%d',
            (int) round($base),
            (int) round(100 - $base),
            $situational,
            $ambushSide !== null ? " (ambush {$ambushSide})" : '',
            (int) round($final),
            (int) round(100 - $final)
        );

        return new Odds($base, $situational, $final, $reason);
    }

    /** Sigmoid base: A's win% from the CC gap. */
    private static function baseFromCc(int $ccA, int $ccB): float
    {
        $diff = $ccA - $ccB;
        return (1.0 / (1.0 + exp(-self::STEEPNESS * $diff))) * 100.0;
    }

    private static function clampPercent(float $p): float
    {
        return max(1.0, min(99.0, $p));
    }
}
