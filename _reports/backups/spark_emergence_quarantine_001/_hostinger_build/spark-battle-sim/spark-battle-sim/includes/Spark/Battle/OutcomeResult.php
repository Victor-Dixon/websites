<?php

declare(strict_types=1);

namespace Spark\Battle;

/**
 * The settled result of the outcome lottery (Step 2c). Holds everything
 * the operator-only SHOWWORK block needs; players never see it.
 */
final class OutcomeResult
{
    /** @var string */
    private $winnerName;
    /** @var int */
    private $roll;
    /** @var int */
    private $thresholdForA;
    /** @var string */
    private $favoriteAName;
    /** @var string */
    private $favoriteBName;

    public function __construct(
        string $winnerName,
        int $roll,
        int $thresholdForA,
        string $favoriteAName,
        string $favoriteBName
    ) {
        $this->winnerName = $winnerName;
        $this->roll = $roll;
        $this->thresholdForA = $thresholdForA;
        $this->favoriteAName = $favoriteAName;
        $this->favoriteBName = $favoriteBName;
    }

    public function winnerName(): string
    {
        return $this->winnerName;
    }

    public function roll(): int
    {
        return $this->roll;
    }

    public function thresholdForA(): int
    {
        return $this->thresholdForA;
    }

    public function fellInBand(): string
    {
        return $this->winnerName === $this->favoriteAName ? 'A' : 'B';
    }
}
