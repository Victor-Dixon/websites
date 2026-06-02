<?php

declare(strict_types=1);

namespace Spark\Battle;

use Spark\Model\Sheet;

/**
 * Hard / soft counters (Battle_Sim Step 1 "Power matchups"). Encoded as
 * flat percentage-point swings to the attacker side. The table is
 * symmetric where the rulebook implies it (fire vs ice, etc.).
 *
 * Key = "AttackerPower|DefenderPower" -> points toward the attacker.
 */
final class HardCounters
{
    /** @var array<string,float> */
    private const COUNTERS = [
        'Pyrokinesis|Cryokinesis' => -10.0,
        'Cryokinesis|Pyrokinesis' => 10.0,
        'Electrokinesis|Hydrokinesis' => 8.0,
        'Hydrokinesis|Electrokinesis' => -6.0,
        'Energy Absorption|Pyrokinesis' => 8.0,
        'Energy Absorption|Electrokinesis' => 8.0,
        'Energy Absorption|Concussive Blasts' => 6.0,
        'Energy Absorption|Kinetic Manipulation' => 6.0,
        'Shadow Control|Laser Light' => -6.0,
        'Laser Light|Shadow Control' => 6.0,
        'Void Grasp|Hard Light' => -4.0,
        'Hard Light|Void Grasp' => 4.0,
        'Sonic Scream|Force Fields' => -4.0,
        'Psychic Assault|Psychic Defense' => -8.0,
        'Mind Control|Psychic Defense' => -6.0,
        'Telepathy|Psychic Defense' => -3.0,
        'Toxic Emission|Force Fields' => -5.0,
        'Gravity Control|Flight' => 4.0,
    ];

    public static function modifier(Sheet $a, Sheet $b): float
    {
        return self::oneWay($a, $b) - self::oneWay($b, $a);
    }

    private static function oneWay(Sheet $att, Sheet $def): float
    {
        $mod = 0.0;
        foreach ($att->powers() as $pa) {
            foreach ($def->powers() as $pb) {
                $key = $pa->name() . '|' . $pb->name();
                if (isset(self::COUNTERS[$key])) {
                    $mod += self::COUNTERS[$key];
                }
            }
        }
        return $mod;
    }
}
