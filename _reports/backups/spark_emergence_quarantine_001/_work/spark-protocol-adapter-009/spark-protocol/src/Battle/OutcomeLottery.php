<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Support\Rng;

/**
 * Step 2: the outcome lottery. The threshold is committed by the Odds
 * object BEFORE this is called; the roll happens here as its own RNG draw.
 * Structurally, the number cannot exist until resolve() asks the Rng for
 * it — the code analogue of the protocol's commit-before-roll safeguard.
 *
 * Rolls 1..thresholdForA hand fighter A the win; the rest hand B the win.
 */
final class OutcomeLottery
{
    public static function resolve(Odds $odds, string $nameA, string $nameB, Rng $rng): OutcomeResult
    {
        $threshold = $odds->thresholdForA();
        $roll = $rng->roll(100);

        $winner = ($roll <= $threshold) ? $nameA : $nameB;

        return new OutcomeResult($winner, $roll, $threshold, $nameA, $nameB);
    }
}
