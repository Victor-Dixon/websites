<?php

declare(strict_types=1);

namespace Spark\Battle;

/**
 * The committed Step 1 split, with its breakdown for the SHOWWORK view.
 * "Favorite A percent" is A's win probability after the arena adjustment;
 * which side is the narrative favorite is simply whoever is above 50.
 */
final class Odds
{
    /** @var float */
    private $basePercent;
    /** @var float */
    private $arenaAdjustment;
    /** @var float */
    private $finalAPercent;
    /** @var string */
    private $reason;

    public function __construct(float $basePercent, float $arenaAdjustment, float $finalAPercent, string $reason)
    {
        $this->basePercent = $basePercent;
        $this->arenaAdjustment = $arenaAdjustment;
        $this->finalAPercent = $finalAPercent;
        $this->reason = $reason;
    }

    public function basePercent(): float
    {
        return $this->basePercent;
    }

    public function arenaAdjustment(): float
    {
        return $this->arenaAdjustment;
    }

    public function favoriteAPercent(): float
    {
        return $this->finalAPercent;
    }

    public function favoriteBPercent(): float
    {
        return 100.0 - $this->finalAPercent;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    /**
     * The committed integer threshold for the lottery: rolls 1..threshold
     * hand A the win, the rest hand B the win.
     */
    public function thresholdForA(): int
    {
        return (int) round($this->finalAPercent);
    }
}
