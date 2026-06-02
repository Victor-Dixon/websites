<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;
use Spark\Support\MtRng;
use Spark\Support\Rng;

/**
 * Orchestrates one full pass of the stochastic protocol:
 *   Step 0  roll the arena
 *   Step 1  assess + commit the odds
 *   Step 2  roll the outcome (a SEPARATE draw, after the commit)
 *
 * It returns the structured result (arena, odds, winner). It does NOT
 * write the narrative — that is the LLM storyteller's job and is out of
 * scope for deterministic code. The split between arena and outcome rolls
 * is enforced by ordering: the outcome Rng draw only happens after the
 * Odds object (and its committed threshold) already exists.
 */
final class BattleSimulator
{
    /** @var Rng */
    private $rng;

    public function __construct(?Rng $rng = null)
    {
        $this->rng = $rng ?? new MtRng();
    }

    public function simulate(Sheet $a, Sheet $b): BattleReport
    {
        // Step 0 — arena (may share one execution; independent of odds).
        $arena = ArenaRoller::roll($this->rng);

        // Step 1 — assess and COMMIT the threshold (lives inside Odds).
        $odds = OddsAssessment::assess($a, $b, $arena);

        // Step 2 — a fresh draw, only now that the threshold is locked.
        $outcome = OutcomeLottery::resolve($odds, $a->maskName(), $b->maskName(), $this->rng);

        return new BattleReport($arena, $odds, $outcome, $a, $b);
    }
}
