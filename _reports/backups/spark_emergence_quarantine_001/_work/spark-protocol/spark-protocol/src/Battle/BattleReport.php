<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;

/**
 * Structured result of one simulated fight. The SHOWWORK block is
 * operator-only tooling; the player-facing layer prints only the
 * narrative (which this code intentionally does not generate).
 */
final class BattleReport
{
    /** @var Arena */
    private $arena;
    /** @var Odds */
    private $odds;
    /** @var OutcomeResult */
    private $outcome;
    /** @var Sheet */
    private $a;
    /** @var Sheet */
    private $b;

    public function __construct(Arena $arena, Odds $odds, OutcomeResult $outcome, Sheet $a, Sheet $b)
    {
        $this->arena = $arena;
        $this->odds = $odds;
        $this->outcome = $outcome;
        $this->a = $a;
        $this->b = $b;
    }

    public function arena(): Arena
    {
        return $this->arena;
    }

    public function odds(): Odds
    {
        return $this->odds;
    }

    public function outcome(): OutcomeResult
    {
        return $this->outcome;
    }

    public function winnerName(): string
    {
        return $this->outcome->winnerName();
    }

    /**
     * Operator-only SHOWWORK block. Never shown to players.
     */
    public function showWork(): string
    {
        return implode("\n", [
            'Arena: ' . $this->arena->location . ', ' . $this->arena->timeOfDay
                . ', ' . $this->arena->weather . ', ' . $this->arena->temperature
                . ' | ' . $this->arena->distance,
            'Odds: ' . $this->odds->reason(),
            sprintf(
                'Roll: committed %d/%d, returned %d -> %s band',
                $this->odds->thresholdForA(),
                100 - $this->odds->thresholdForA(),
                $this->outcome->roll(),
                $this->outcome->fellInBand()
            ),
            'Winner: ' . $this->outcome->winnerName(),
        ]);
    }
}
