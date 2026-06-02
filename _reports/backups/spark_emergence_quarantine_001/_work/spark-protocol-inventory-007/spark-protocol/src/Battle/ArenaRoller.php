<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Support\Rng;

/**
 * Step 0: roll the world. Seven executed rolls — location, time, weather,
 * temperature, two positions, one shared distance — all drawn from the Rng
 * so the arena is reproducible in tests and genuinely random in production.
 */
final class ArenaRoller
{
    private const LOCATIONS = [
        1 => 'City docks', 2 => 'Abandoned warehouse', 3 => 'Skyscraper rooftop',
        4 => 'Active construction site', 5 => 'Countryside farm', 6 => 'Downtown intersection',
        7 => 'Subway platform / tunnel', 8 => 'River bridge', 9 => 'Multi-level parking garage',
        10 => 'Shopping mall atrium', 11 => 'Power substation', 12 => 'Stadium field',
        13 => 'Forest edge / treeline', 14 => 'Industrial refinery', 15 => 'Quarry / gravel pit',
        16 => 'Hospital district at night', 17 => 'Train yard', 18 => 'Frozen reservoir (seasonal)',
        19 => 'Old town square', 20 => 'Wind farm on open hill',
    ];

    private const TIMES = [
        1 => 'Pre-dawn', 2 => 'Morning', 3 => 'Midday', 4 => 'Afternoon', 5 => 'Dusk', 6 => 'Night',
    ];

    private const WEATHER = [
        1 => 'Clear and dry', 2 => 'Overcast, still', 3 => 'Light rain', 4 => 'Heavy rain / storm',
        5 => 'Fog', 6 => 'High wind', 7 => 'Snow / sleet', 8 => 'Heat haze',
    ];

    private const TEMPS = [
        1 => 'Freezing', 2 => 'Cold', 3 => 'Cool', 4 => 'Mild', 5 => 'Warm', 6 => 'Hot',
    ];

    private const POSITIONS = [
        1 => Arena::POS_OPEN, 2 => Arena::POS_COVER, 3 => Arena::POS_ELEVATED, 4 => Arena::POS_BELOW,
    ];

    private const DISTANCES = [1 => 'Close', 2 => 'Mid', 3 => 'Long', 4 => 'Maximum'];

    public static function roll(Rng $rng): Arena
    {
        $location = self::LOCATIONS[$rng->roll(20)];
        $time = self::TIMES[$rng->roll(6)];
        $weather = self::WEATHER[$rng->roll(8)];
        $temp = self::TEMPS[$rng->roll(6)];
        $posA = self::POSITIONS[$rng->roll(4)];
        $posB = self::POSITIONS[$rng->roll(4)];
        $distance = self::DISTANCES[$rng->roll(4)];

        return new Arena($location, $time, $weather, $temp, $posA, $posB, $distance);
    }
}
