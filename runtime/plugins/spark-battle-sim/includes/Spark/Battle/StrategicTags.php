<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;

/**
 * Strategic-threat tags can flip duel math at the margins. The rulebook
 * is explicit that the tag raises AEGIS *posture*, not Combat Capability,
 * so the duel-scale nudge here is small and never moves a band on its own.
 */
final class StrategicTags
{
    /** @var array<string,float> */
    private const DUEL_NUDGE = [
        'Shadow Control' => 3.0,
        'Energy Absorption' => 3.0,
        'Mind Control' => 3.0,
        'Duplication' => 2.0,
        'Healing Factor' => 2.0,
        'Teleportation' => 2.0,
        'Adaptive Biology' => 2.0,
        'Shapeshifting' => 1.0,
        'Pheromone Control' => 1.0,
    ];

    public static function modifier(Sheet $a, Sheet $b): float
    {
        return self::sumFor($a) - self::sumFor($b);
    }

    private static function sumFor(Sheet $sheet): float
    {
        $total = 0.0;
        foreach ($sheet->strategicTags() as $tag) {
            $total += self::DUEL_NUDGE[$tag] ?? 0.0;
        }
        return $total;
    }
}
