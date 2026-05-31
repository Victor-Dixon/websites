<?php

declare(strict_types=1);

namespace Spark\Engine;

use Spark\Model\Power;
use Spark\Model\PowerRegistry;

/**
 * Step 9: read Combat Capability via Spine -> Center -> Bench -> Cap.
 *
 * Pure and deterministic. Produces a value object carrying the full
 * breakdown so tests (and a future SHOWWORK view) can inspect each stage.
 */
final class CombatCapability
{
    /** @var int */
    private $value;
    /** @var int */
    private $center;
    /** @var int */
    private $bench;
    /** @var float */
    private $attackSlotValue;
    /** @var float */
    private $defenseSlotValue;
    /** @var Power|null */
    private $attackPower;
    /** @var Power|null */
    private $defensePower;
    /** @var bool */
    private $flooredApplied;

    private function __construct(
        int $value,
        int $center,
        int $bench,
        float $attackSlotValue,
        float $defenseSlotValue,
        ?Power $attackPower,
        ?Power $defensePower,
        bool $flooredApplied
    ) {
        $this->value = $value;
        $this->center = $center;
        $this->bench = $bench;
        $this->attackSlotValue = $attackSlotValue;
        $this->defenseSlotValue = $defenseSlotValue;
        $this->attackPower = $attackPower;
        $this->defensePower = $defensePower;
        $this->flooredApplied = $flooredApplied;
    }

    /**
     * @param Power[] $powers
     */
    public static function read(array $powers): self
    {
        [$attack, $defense] = self::fillSpine($powers);

        $slotEmpty = ($attack === null || $defense === null);

        $attackVal = $attack ? self::roundHalfUp($attack->spineValueRaw()) : 0.0;
        $defenseVal = $defense ? self::roundHalfUp($defense->spineValueRaw()) : 0.0;

        if ($slotEmpty) {
            // One (or zero) slots filled: the protocol's worked reads take the
            // center from the UNROUNDED tier*multiplier*10 product, so a solo
            // Telepathy T5 reads 5*0.75*10 = 37.5 -> 38, not the double-rounded 40.
            $occupied = $attack ?? $defense;
            $center = $occupied
                ? (int) self::roundHalfUp($occupied->spineValueRaw() * 10)
                : 0;
        } else {
            $spine = self::roundHalfUp($attackVal + $defenseVal);
            $center = (int) self::roundHalfUp($spine * 10);
        }

        // Bench: every manifested power not already in a spine slot.
        $bench = 0;
        foreach ($powers as $p) {
            if ($p === $attack || $p === $defense) {
                continue;
            }
            $bench += $p->benchValue();
        }

        $raw = $center + $bench;

        // Sanctioned exception a: HIGHEST-TIER FLOOR (only when a slot is empty).
        $floored = false;
        if ($slotEmpty) {
            $floor = self::highestTierFloor($powers);
            if ($floor > $raw) {
                $raw = $floor;
                $floored = true;
            }
        }

        $value = min(100, max(0, (int) $raw));

        return new self(
            $value,
            $center,
            $bench,
            $attackVal,
            $defenseVal,
            $attack,
            $defense,
            $floored
        );
    }

    /**
     * Fill the two spine slots to maximise spine value under the rules:
     *   - one power fills at most one slot
     *   - ATTACK-role powers prefer attack, DEFENSE-role prefer defense,
     *     EITHER goes where it serves best on this sheet
     * We choose the assignment of distinct powers (a -> attack, d -> defense)
     * maximising attackValue + defenseValue, respecting role preferences as
     * the protocol's "obvious attackers fill attack" guidance — which, for
     * the scaled table, coincides with the value-maximising legal assignment.
     *
     * @param Power[] $powers
     * @return array{0:?Power,1:?Power}
     */
    private static function fillSpine(array $powers): array
    {
        if (count($powers) === 0) {
            return [null, null];
        }

        $best = ['attack' => null, 'defense' => null, 'sum' => -1.0];

        // Candidate attack powers: anything not pure-DEFENSE role.
        // Candidate defense powers: anything not pure-ATTACK role.
        foreach ($powers as $ai => $a) {
            if ($a->role() === PowerRegistry::ROLE_DEFENSE) {
                continue; // obvious defenders never sit in attack
            }
            // attack-only single fill (defense empty)
            $aVal = self::roundHalfUp($a->spineValueRaw());
            if ($aVal > $best['sum']) {
                $best = ['attack' => $a, 'defense' => null, 'sum' => $aVal];
            }
            foreach ($powers as $di => $d) {
                if ($di === $ai) {
                    continue; // one power, one slot
                }
                if ($d->role() === PowerRegistry::ROLE_ATTACK) {
                    continue; // obvious attackers never sit in defense
                }
                $sum = $aVal + self::roundHalfUp($d->spineValueRaw());
                if ($sum > $best['sum']) {
                    $best = ['attack' => $a, 'defense' => $d, 'sum' => $sum];
                }
            }
        }

        // defense-only single fill (attack empty) — when every power is DEFENSE-role.
        foreach ($powers as $d) {
            if ($d->role() === PowerRegistry::ROLE_ATTACK) {
                continue;
            }
            $dVal = self::roundHalfUp($d->spineValueRaw());
            if ($dVal > $best['sum']) {
                $best = ['attack' => null, 'defense' => $d, 'sum' => $dVal];
            }
        }

        return [$best['attack'], $best['defense']];
    }

    /**
     * Sanctioned exception a: floor = highest power's tier * scaled multiplier * 10.
     *
     * @param Power[] $powers
     */
    private static function highestTierFloor(array $powers): int
    {
        $best = 0.0;
        foreach ($powers as $p) {
            $v = $p->tier() * PowerRegistry::scaledMultiplier($p->name(), $p->tier()) * 10;
            if ($v > $best) {
                $best = $v;
            }
        }
        return (int) self::roundHalfUp($best);
    }

    /** Round half up (the protocol's stated rounding). */
    private static function roundHalfUp(float $n): float
    {
        return floor($n + 0.5);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function center(): int
    {
        return $this->center;
    }

    public function bench(): int
    {
        return $this->bench;
    }

    public function attackSlotValue(): float
    {
        return $this->attackSlotValue;
    }

    public function defenseSlotValue(): float
    {
        return $this->defenseSlotValue;
    }

    public function attackPower(): ?Power
    {
        return $this->attackPower;
    }

    public function defensePower(): ?Power
    {
        return $this->defensePower;
    }

    public function floorApplied(): bool
    {
        return $this->flooredApplied;
    }
}
