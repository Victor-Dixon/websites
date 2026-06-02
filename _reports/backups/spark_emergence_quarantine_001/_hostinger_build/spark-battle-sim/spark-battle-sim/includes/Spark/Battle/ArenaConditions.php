<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;

/**
 * Turns a rolled Arena into condition tags and scores how well each
 * fighter's kit fits the world (percentage points). Pure functions.
 */
final class ArenaConditions
{
    /**
     * Per-power arena condition modifiers, in percentage points.
     *
     * @var array<string,array<string,float>>
     */
    private const ARENA_MODS = [
        'Flight' => ['open_sky' => 8.0, 'high_wind' => -6.0, 'tight_space' => -8.0, 'indoor' => -4.0, 'fog' => -3.0],
        'Super Speed' => ['open_ground' => 5.0, 'tight_space' => -6.0, 'slick_surface' => -4.0, 'long_distance' => 3.0, 'debris' => -2.0],
        'Pyrokinesis' => ['rain' => -8.0, 'heavy_rain' => -12.0, 'wet' => -5.0, 'standing_water' => -6.0, 'open' => 3.0, 'heat_haze' => 2.0],
        'Cryokinesis' => ['hot' => -5.0, 'heat_haze' => -4.0, 'warm' => -2.0, 'snow' => 4.0, 'cold' => 3.0, 'wet' => 2.0],
        'Electrokinesis' => ['wet' => 6.0, 'standing_water' => 8.0, 'rain' => 4.0, 'dry' => -3.0, 'snow' => -2.0],
        'Hydrokinesis' => ['standing_water' => 8.0, 'wet' => 5.0, 'rain' => 4.0, 'dry' => -4.0, 'heat_haze' => -3.0],
        'Invisibility' => ['dark' => 5.0, 'fog' => 4.0, 'night' => 3.0, 'bright' => -3.0, 'midday' => -2.0],
        'Shadow Control' => ['night' => 7.0, 'dark' => 6.0, 'fog' => 4.0, 'midday' => -5.0, 'bright' => -4.0],
        'Laser Light' => ['fog' => -6.0, 'rain' => -4.0, 'dark' => 3.0],
        'Invulnerability' => ['debris_rich' => 2.0, 'tight_space' => 1.0],
        'Teleportation' => ['tight_space' => 4.0, 'open' => -2.0, 'long_distance' => 3.0],
        'Danger Sense' => ['fog' => -3.0, 'chaos' => -2.0, 'open' => 2.0],
        'Enhanced Senses' => ['fog' => -4.0, 'dark' => -3.0, 'open' => 2.0],
        'Weather Control' => ['open' => 5.0, 'tight_space' => -4.0],
        'Nature Control' => ['open' => 3.0, 'urban' => -3.0, 'forest' => 5.0],
        'Force Fields' => ['open' => -2.0, 'tight_space' => 3.0],
        'Vibration Control' => ['concrete' => 4.0, 'steel' => 3.0, 'open_ground' => -2.0],
    ];

    /**
     * Derive condition tags from a rolled arena.
     *
     * @return string[]
     */
    public static function derive(Arena $arena): array
    {
        $c = [];

        if (in_array($arena->timeOfDay, ['Pre-dawn', 'Night'], true)) {
            $c[] = 'dark';
            $c[] = 'night';
        }
        if ($arena->timeOfDay === 'Midday') {
            $c[] = 'bright';
            $c[] = 'midday';
        }

        $w = strtolower($arena->weather);
        if (strpos($w, 'clear') !== false) {
            $c[] = 'open';
        }
        if (strpos($w, 'rain') !== false && strpos($w, 'heavy') === false) {
            $c[] = 'rain';
            $c[] = 'wet';
        }
        if (strpos($w, 'storm') !== false || strpos($w, 'heavy rain') !== false) {
            $c[] = 'heavy_rain';
            $c[] = 'wet';
        }
        if (strpos($w, 'fog') !== false) {
            $c[] = 'fog';
        }
        if (strpos($w, 'wind') !== false) {
            $c[] = 'high_wind';
            $c[] = 'debris';
        }
        if (strpos($w, 'snow') !== false || strpos($w, 'sleet') !== false) {
            $c[] = 'snow';
            $c[] = 'cold';
            $c[] = 'wet';
        }
        if (strpos($w, 'heat haze') !== false) {
            $c[] = 'heat_haze';
            $c[] = 'hot';
        }

        $loc = strtolower($arena->location);
        $has = static function (array $needles) use ($loc): bool {
            foreach ($needles as $n) {
                if (strpos($loc, $n) !== false) {
                    return true;
                }
            }
            return false;
        };
        if ($has(['wind farm', 'rooftop', 'countryside', 'bridge', 'farm', 'hill'])) {
            $c[] = 'open_sky';
            $c[] = 'open_ground';
            $c[] = 'open';
        }
        if ($has(['warehouse', 'garage', 'subway', 'mall', 'tunnel'])) {
            $c[] = 'tight_space';
            $c[] = 'indoor';
            $c[] = 'concrete';
        }
        if ($has(['docks', 'reservoir'])) {
            $c[] = 'standing_water';
            $c[] = 'wet';
        }
        if ($has(['construction', 'industrial', 'refinery', 'substation'])) {
            $c[] = 'debris_rich';
            $c[] = 'steel';
            $c[] = 'concrete';
        }
        if ($has(['forest', 'treeline'])) {
            $c[] = 'forest';
        }
        if ($has(['downtown', 'city', 'town square', 'intersection'])) {
            $c[] = 'urban';
        }
        if ($has(['quarry', 'parking'])) {
            $c[] = 'concrete';
        }
        if ($has(['stadium', 'train yard'])) {
            $c[] = 'open_ground';
        }

        if ($arena->distance === 'Long' || $arena->distance === 'Maximum') {
            $c[] = 'long_distance';
        }
        if ($arena->distance === 'Close') {
            $c[] = 'tight_space';
        }

        if ($arena->temperature === 'Hot') {
            $c[] = 'hot';
            $c[] = 'heat_haze';
        }
        if ($arena->temperature === 'Freezing') {
            $c[] = 'cold';
            $c[] = 'snow';
        }

        return array_values(array_unique($c));
    }

    /**
     * Total arena modifier (percentage points) for one sheet's kit.
     */
    public static function modifierFor(Sheet $sheet, Arena $arena): float
    {
        $conds = self::derive($arena);
        $total = 0.0;
        foreach ($sheet->powers() as $p) {
            $mods = self::ARENA_MODS[$p->name()] ?? [];
            foreach ($conds as $cond) {
                if (isset($mods[$cond])) {
                    $total += $mods[$cond];
                }
            }
        }
        return $total;
    }
}
