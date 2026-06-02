<?php

declare(strict_types=1);

namespace Spark\Model;

/**
 * Canonical data for all 48 powers, transcribed from Spark Protocol v8.5.
 *
 * This is the single source of truth shared by every subsystem:
 *   - duel WEIGHT class (FULL / HALF / QUARTER) — Step 9 + Exchange
 *   - strategic-threat flag                      — Step 11
 *   - spine SLOT role (ATTACK / DEFENSE / EITHER) — Step 9 slot sorting
 *   - combat AXIS                                 — Battle compounding magnification
 *
 * Nothing here changes per fighter; it is the rulebook, not a sheet.
 */
final class PowerRegistry
{
    public const WEIGHT_FULL    = 'FULL';
    public const WEIGHT_HALF    = 'HALF';
    public const WEIGHT_QUARTER = 'QUARTER';

    public const ROLE_ATTACK  = 'ATTACK';
    public const ROLE_DEFENSE  = 'DEFENSE';
    public const ROLE_EITHER  = 'EITHER';

    /**
     * Step 9 duel-weight classification (verbatim from the protocol lists).
     *
     * @var array<string,string>
     */
    private const WEIGHT = [
        // FULL — directly hurts or directly stops an equal opponent now.
        'Super Strength' => self::WEIGHT_FULL,
        'Invulnerability' => self::WEIGHT_FULL,
        'Density Control' => self::WEIGHT_FULL,
        'Unstoppable Momentum' => self::WEIGHT_FULL,
        'Enhanced Reflexes' => self::WEIGHT_FULL,
        'Vibration Control' => self::WEIGHT_FULL,
        'Concussive Blasts' => self::WEIGHT_FULL,
        'Pyrokinesis' => self::WEIGHT_FULL,
        'Cryokinesis' => self::WEIGHT_FULL,
        'Electrokinesis' => self::WEIGHT_FULL,
        'Sonic Scream' => self::WEIGHT_FULL,
        'Hydrokinesis' => self::WEIGHT_FULL,
        'Kinetic Manipulation' => self::WEIGHT_FULL,
        'Nature Control' => self::WEIGHT_FULL,
        'Mind Control' => self::WEIGHT_FULL,
        'Telekinesis' => self::WEIGHT_FULL,
        'Psychic Assault' => self::WEIGHT_FULL,
        'Hard Light' => self::WEIGHT_FULL,
        'Laser Light' => self::WEIGHT_FULL,
        'Void Grasp' => self::WEIGHT_FULL,

        // HALF — shapes the duel but ends nothing by itself.
        'Giant Size' => self::WEIGHT_HALF,
        'Elasticity' => self::WEIGHT_HALF,
        'Super Speed' => self::WEIGHT_HALF,
        'Flight' => self::WEIGHT_HALF,
        'Danger Sense' => self::WEIGHT_HALF,
        'Teleportation' => self::WEIGHT_HALF,
        'Intangibility' => self::WEIGHT_HALF,
        'Force Fields' => self::WEIGHT_HALF,
        'Healing Factor' => self::WEIGHT_HALF,
        'Gravity Control' => self::WEIGHT_HALF,
        'Magnetism' => self::WEIGHT_HALF,
        'Weather Control' => self::WEIGHT_HALF,
        'Animal Form' => self::WEIGHT_HALF,
        'Adaptive Biology' => self::WEIGHT_HALF,
        'Illusion' => self::WEIGHT_HALF,
        'Psychic Defense' => self::WEIGHT_HALF,
        'Enhanced Senses' => self::WEIGHT_HALF,
        'Energy Absorption' => self::WEIGHT_HALF,
        'Shadow Control' => self::WEIGHT_HALF,
        'Toxic Emission' => self::WEIGHT_HALF,

        // QUARTER — wins campaigns, not duels.
        'Wall-Crawling' => self::WEIGHT_QUARTER,
        'Invisibility' => self::WEIGHT_QUARTER,
        'Shrinking' => self::WEIGHT_QUARTER,
        'Portal Creation' => self::WEIGHT_QUARTER,
        'Duplication' => self::WEIGHT_QUARTER,
        'Shapeshifting' => self::WEIGHT_QUARTER,
        'Pheromone Control' => self::WEIGHT_QUARTER,
        'Telepathy' => self::WEIGHT_QUARTER,
    ];

    /**
     * Step 11 strategic-threat powers.
     *
     * @var array<string,bool>
     */
    private const STRATEGIC = [
        'Pheromone Control' => true,
        'Duplication' => true,
        'Healing Factor' => true,
        'Teleportation' => true,
        'Adaptive Biology' => true,
        'Shapeshifting' => true,
        'Mind Control' => true,
        'Shadow Control' => true,
        'Energy Absorption' => true,
    ];

    /**
     * Step 9 spine-slot role. Powers not listed default to EITHER.
     *
     * @var array<string,string>
     */
    private const ROLE = [
        // Obvious attackers.
        'Super Strength' => self::ROLE_ATTACK,
        'Unstoppable Momentum' => self::ROLE_ATTACK,
        'Vibration Control' => self::ROLE_ATTACK,
        'Concussive Blasts' => self::ROLE_ATTACK,
        'Pyrokinesis' => self::ROLE_ATTACK,
        'Cryokinesis' => self::ROLE_ATTACK,
        'Electrokinesis' => self::ROLE_ATTACK,
        'Sonic Scream' => self::ROLE_ATTACK,
        'Hydrokinesis' => self::ROLE_ATTACK,
        'Laser Light' => self::ROLE_ATTACK,
        'Void Grasp' => self::ROLE_ATTACK,
        'Toxic Emission' => self::ROLE_ATTACK,
        'Mind Control' => self::ROLE_ATTACK,
        'Psychic Assault' => self::ROLE_ATTACK,

        // Obvious defenders.
        'Invulnerability' => self::ROLE_DEFENSE,
        'Force Fields' => self::ROLE_DEFENSE,
        'Healing Factor' => self::ROLE_DEFENSE,
        'Adaptive Biology' => self::ROLE_DEFENSE,
        'Psychic Defense' => self::ROLE_DEFENSE,
        // everything else => EITHER (resolved by getRole()).
    ];

    /**
     * Combat axis for the battle engine's compounding magnification.
     *
     * @var array<string,string>
     */
    private const AXIS = [
        'Super Strength' => 'phys_off', 'Unstoppable Momentum' => 'phys_off',
        'Vibration Control' => 'phys_off', 'Giant Size' => 'phys_off',
        'Kinetic Manipulation' => 'phys_off', 'Void Grasp' => 'phys_off',
        'Nature Control' => 'phys_off', 'Animal Form' => 'phys_off',
        'Telekinesis' => 'phys_off', 'Hard Light' => 'phys_off',
        'Magnetism' => 'phys_off', 'Gravity Control' => 'phys_off',

        'Invulnerability' => 'phys_def', 'Density Control' => 'phys_def',
        'Force Fields' => 'phys_def', 'Healing Factor' => 'phys_def',
        'Adaptive Biology' => 'phys_def', 'Intangibility' => 'phys_def',
        'Elasticity' => 'phys_def',

        'Concussive Blasts' => 'eng_off', 'Pyrokinesis' => 'eng_off',
        'Cryokinesis' => 'eng_off', 'Electrokinesis' => 'eng_off',
        'Sonic Scream' => 'eng_off', 'Hydrokinesis' => 'eng_off',
        'Laser Light' => 'eng_off', 'Weather Control' => 'eng_off',
        'Toxic Emission' => 'eng_off', 'Energy Absorption' => 'eng_off',

        'Telepathy' => 'mental', 'Mind Control' => 'mental',
        'Psychic Assault' => 'mental', 'Psychic Defense' => 'mental',
        'Illusion' => 'mental', 'Pheromone Control' => 'mental',

        'Super Speed' => 'mobility', 'Flight' => 'mobility',
        'Teleportation' => 'mobility', 'Portal Creation' => 'mobility',
        'Wall-Crawling' => 'mobility', 'Enhanced Reflexes' => 'mobility',
        'Danger Sense' => 'mobility',

        'Invisibility' => 'stealth', 'Shrinking' => 'stealth',
        'Shapeshifting' => 'stealth',
        'Enhanced Senses' => 'perception',
    ];

    /**
     * Scaled duel-weight multiplier used by the SPINE (Step 9).
     * Indexed [weight][tier 1..5].
     *
     * @var array<string,array<int,float>>
     */
    private const SCALED_MULTIPLIER = [
        self::WEIGHT_FULL    => [1 => 1.00, 2 => 1.00, 3 => 1.00, 4 => 1.00, 5 => 1.00],
        self::WEIGHT_HALF    => [1 => 0.50, 2 => 0.50, 3 => 0.50, 4 => 0.75, 5 => 1.00],
        self::WEIGHT_QUARTER => [1 => 0.25, 2 => 0.25, 3 => 0.25, 4 => 0.50, 5 => 0.75],
    ];

    /**
     * BENCH grid = ceil(tier * scaled multiplier). Indexed [weight][tier].
     *
     * @var array<string,array<int,int>>
     */
    private const BENCH_GRID = [
        self::WEIGHT_FULL    => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5],
        self::WEIGHT_HALF    => [1 => 1, 2 => 1, 3 => 2, 4 => 3, 5 => 5],
        self::WEIGHT_QUARTER => [1 => 1, 2 => 1, 3 => 1, 4 => 2, 5 => 4],
    ];

    public static function isKnown(string $power): bool
    {
        return isset(self::WEIGHT[$power]);
    }

    public static function getWeight(string $power): string
    {
        if (!isset(self::WEIGHT[$power])) {
            throw new \InvalidArgumentException("Unknown power: {$power}");
        }
        return self::WEIGHT[$power];
    }

    public static function isStrategic(string $power): bool
    {
        return self::STRATEGIC[$power] ?? false;
    }

    public static function getRole(string $power): string
    {
        return self::ROLE[$power] ?? self::ROLE_EITHER;
    }

    public static function getAxis(string $power): ?string
    {
        return self::AXIS[$power] ?? null;
    }

    /**
     * Scaled spine multiplier for a (power, tier) pair.
     */
    public static function scaledMultiplier(string $power, int $tier): float
    {
        self::assertTier($tier);
        return self::SCALED_MULTIPLIER[self::getWeight($power)][$tier];
    }

    /**
     * Bench-grid value for a (power, tier) pair.
     */
    public static function benchValue(string $power, int $tier): int
    {
        self::assertTier($tier);
        return self::BENCH_GRID[self::getWeight($power)][$tier];
    }

    /**
     * All known power names.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return array_keys(self::WEIGHT);
    }

    private static function assertTier(int $tier): void
    {
        if ($tier < 1 || $tier > 5) {
            throw new \InvalidArgumentException("Tier must be 1-5, got {$tier}");
        }
    }
}
