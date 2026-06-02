<?php

declare(strict_types=1);

namespace Spark\Exchange;

use Spark\Model\PowerRegistry;

/**
 * The FIXED Exchange capacity-cost table (notoriety price deferred).
 *
 * Charged against spare capacity = 100 - CC. Cost depends on both the
 * tier delivered and the power's duel weight. Tables are transcribed
 * verbatim from Exchange_ShopV2 and pinned by the unit tests.
 */
final class CapacityCost
{
    /**
     * New-power cost AT T1..T5, indexed [weight][tier].
     *
     * @var array<string,array<int,int>>
     */
    private const NEW_POWER = [
        PowerRegistry::WEIGHT_FULL    => [1 => 2, 2 => 5, 3 => 9, 4 => 18, 5 => 30],
        PowerRegistry::WEIGHT_HALF    => [1 => 1, 2 => 3, 3 => 5, 4 => 11, 5 => 18],
        PowerRegistry::WEIGHT_QUARTER => [1 => 1, 2 => 2, 3 => 3, 4 => 6,  5 => 11],
    ];

    /**
     * Tier-advancement cost to reach T2..T5, indexed [weight][targetTier].
     *
     * @var array<string,array<int,int>>
     */
    private const ADVANCE = [
        PowerRegistry::WEIGHT_FULL    => [2 => 3, 3 => 4, 4 => 9, 5 => 12],
        PowerRegistry::WEIGHT_HALF    => [2 => 2, 3 => 2, 4 => 6, 5 => 7],
        PowerRegistry::WEIGHT_QUARTER => [2 => 1, 3 => 1, 4 => 3, 5 => 5],
    ];

    public static function newPower(string $weight, int $tier): int
    {
        self::assert($weight, $tier, 1);
        return self::NEW_POWER[$weight][$tier];
    }

    public static function advance(string $weight, int $targetTier): int
    {
        self::assert($weight, $targetTier, 2);
        return self::ADVANCE[$weight][$targetTier];
    }

    private static function assert(string $weight, int $tier, int $min): void
    {
        if (!isset(self::NEW_POWER[$weight])) {
            throw new \InvalidArgumentException("Unknown weight: {$weight}");
        }
        if ($tier < $min || $tier > 5) {
            throw new \InvalidArgumentException("Tier out of range: {$tier}");
        }
    }
}
