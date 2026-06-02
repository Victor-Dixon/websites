<?php

declare(strict_types=1);

namespace Spark\Model;

/**
 * The eight Spark domains and the six sub-affinity powers each contains,
 * transcribed from the Part VIII flavor worksheet (Titan: Str Inv Den Gig
 * Ela Mom, etc.) expanded to full power names.
 */
final class DomainRegistry
{
    public const DOMAINS = ['Titan', 'Velocity', 'Energy', 'Specter', 'Duality', 'Omni', 'Primal', 'Mind'];

    /**
     * @var array<string,string[]>
     */
    private const POWERS = [
        'Titan'    => ['Super Strength', 'Invulnerability', 'Density Control', 'Giant Size', 'Elasticity', 'Unstoppable Momentum'],
        'Velocity' => ['Super Speed', 'Flight', 'Enhanced Reflexes', 'Danger Sense', 'Wall-Crawling', 'Vibration Control'],
        'Energy'   => ['Concussive Blasts', 'Pyrokinesis', 'Cryokinesis', 'Electrokinesis', 'Sonic Scream', 'Hydrokinesis'],
        'Specter'  => ['Telepathy', 'Intangibility', 'Invisibility', 'Shrinking', 'Enhanced Senses', 'Portal Creation'],
        'Duality'  => ['Hard Light', 'Laser Light', 'Energy Absorption', 'Shadow Control', 'Toxic Emission', 'Void Grasp'],
        'Omni'     => ['Kinetic Manipulation', 'Force Fields', 'Healing Factor', 'Gravity Control', 'Magnetism', 'Duplication'],
        'Primal'   => ['Shapeshifting', 'Nature Control', 'Weather Control', 'Animal Form', 'Adaptive Biology', 'Pheromone Control'],
        'Mind'     => ['Telepathy', 'Mind Control', 'Telekinesis', 'Illusion', 'Psychic Assault', 'Psychic Defense'],
    ];

    public static function isDomain(string $domain): bool
    {
        return in_array($domain, self::DOMAINS, true);
    }

    /**
     * @return string[]
     */
    public static function powersOf(string $domain): array
    {
        if (!isset(self::POWERS[$domain])) {
            throw new \InvalidArgumentException("Unknown domain: {$domain}");
        }
        return self::POWERS[$domain];
    }

    public static function containsPower(string $domain, string $power): bool
    {
        return in_array($power, self::powersOf($domain), true);
    }
}
