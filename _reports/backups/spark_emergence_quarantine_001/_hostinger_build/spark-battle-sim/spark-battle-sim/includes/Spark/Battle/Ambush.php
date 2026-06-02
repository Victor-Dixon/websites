<?php

declare(strict_types=1);

namespace Spark\Battle;

/**
 * Ambush (Battle_Sim Step 0). Never a power, never its own roll: it falls
 * out of the rolled positions and conditions. Needs BOTH halves at once —
 * one fighter concealed (cover or below/sunken) while the other is in the
 * open, AND conditions that hide an approach (pre-dawn/night, fog, or
 * heavy rain/storm). Worth a real opening edge under the same arena cap.
 */
final class Ambush
{
    private const POINTS = 15.0;

    /**
     * @return array{0:float,1:?string} [points toward A, side label or null]
     */
    public static function modifier(Arena $arena): array
    {
        $w = strtolower($arena->weather);
        $lowVis = in_array($arena->timeOfDay, ['Pre-dawn', 'Night'], true)
            || strpos($w, 'fog') !== false
            || strpos($w, 'storm') !== false
            || strpos($w, 'heavy rain') !== false;

        if (!$lowVis) {
            return [0.0, null];
        }

        $concealed = [Arena::POS_COVER, Arena::POS_BELOW];
        $open = [Arena::POS_OPEN];

        $aConcealed = in_array($arena->posA, $concealed, true);
        $bConcealed = in_array($arena->posB, $concealed, true);
        $aOpen = in_array($arena->posA, $open, true);
        $bOpen = in_array($arena->posB, $open, true);

        if ($aConcealed && $bOpen) {
            return [self::POINTS, 'A'];
        }
        if ($bConcealed && $aOpen) {
            return [-self::POINTS, 'B'];
        }
        return [0.0, null];
    }
}
