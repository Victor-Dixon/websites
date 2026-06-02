<?php

declare(strict_types=1);

namespace Spark\Battle;

/**
 * The rolled arena (Battle_Sim Step 0). A plain data holder; the rolls
 * that produce it live in ArenaRoller so this stays trivially testable.
 */
final class Arena
{
    public const POS_OPEN      = 'Ground level, in the open';
    public const POS_COVER     = 'Ground level, behind hard cover';
    public const POS_ELEVATED  = 'Elevated';
    public const POS_BELOW     = 'Below / sunken';

    /** @var string */
    public $location;
    /** @var string */
    public $timeOfDay;
    /** @var string */
    public $weather;
    /** @var string */
    public $temperature;
    /** @var string */
    public $posA;
    /** @var string */
    public $posB;
    /** @var string */
    public $distance;

    public function __construct(
        string $location,
        string $timeOfDay,
        string $weather,
        string $temperature,
        string $posA,
        string $posB,
        string $distance
    ) {
        $this->location = $location;
        $this->timeOfDay = $timeOfDay;
        $this->weather = $weather;
        $this->temperature = $temperature;
        $this->posA = $posA;
        $this->posB = $posB;
        $this->distance = $distance;
    }
}
