<?php

declare(strict_types=1);

namespace Spark\Engine;

use Spark\Model\DomainRegistry;
use Spark\Model\Power;
use Spark\Model\Sheet;

/**
 * Generates a locked Sheet from scored domain totals + flavor vectors.
 *
 * This is the deterministic back half of the Spark Protocol (Steps 2-11).
 * It does NOT model the 68-question intake (Step 1) — that is the human
 * front-end; the facilitator hands us the totals. Given those, every step
 * here is a direct, testable read of the rulebook.
 */
final class SheetGenerator
{
    private const MANIFEST_FLAVOR_THRESHOLD = 2; // Step 6
    private const GATE_RATIO = 0.25;             // Step 3

    /** Step 2: map a domain score to its tier. */
    public static function scoreToTier(int $score): int
    {
        if ($score <= 5) {
            return 1;
        }
        if ($score <= 12) {
            return 2;
        }
        if ($score <= 18) {
            return 3;
        }
        if ($score <= 27) {
            return 4;
        }
        return 5;
    }

    /**
     * Step 3: which domains manifest (highest always; others >= 25% of it).
     *
     * @return string[]
     */
    public static function manifestedDomains(DomainScores $scores): array
    {
        $highest = $scores->highest();
        if ($highest === 0) {
            return [];
        }
        $threshold = $highest * self::GATE_RATIO;

        $manifested = [];
        foreach (DomainRegistry::DOMAINS as $domain) {
            $score = $scores->get($domain);
            if ($score === $highest || $score >= $threshold) {
                if ($score > 0) {
                    $manifested[] = $domain;
                }
            }
        }
        return $manifested;
    }

    /** Step 8: Spark Signature (psychology print; never a fight stat). */
    public static function sparkSignature(int $highestTier, int $secondTier, int $powerCount): int
    {
        $raw = 70 + ($highestTier * 2.5) + ($secondTier * 1) + $powerCount;
        return (int) floor($raw + 0.5);
    }

    /** Step 8: Cast descriptor from manifested-domain count. */
    public static function cast(int $manifestedDomainCount): string
    {
        if ($manifestedDomainCount <= 1) {
            return 'Singular';
        }
        if ($manifestedDomainCount === 2) {
            return 'Focused';
        }
        if ($manifestedDomainCount === 3) {
            return 'Versatile';
        }
        return 'Manifold';
    }

    /**
     * Full generation: Steps 2-11 -> locked Sheet.
     *
     * @param array<string,array<string,int>> $flavor
     *        domain => [ powerName => flavorScore(0-5) ]
     */
    public static function generate(string $maskName, DomainScores $scores, array $flavor): Sheet
    {
        $manifestedDomains = self::manifestedDomains($scores);

        /** @var Power[] $powers */
        $powers = [];
        $domainTiers = []; // manifested domain => tier

        foreach ($manifestedDomains as $domain) {
            $tier = self::scoreToTier($scores->get($domain));
            $domainTiers[$domain] = $tier;

            $vector = $flavor[$domain] ?? [];
            $manifestedHere = self::powersForDomain($domain, $vector);

            foreach ($manifestedHere as $powerName) {
                // A power takes its DOMAIN's tier (Step 6: the lead drives it).
                if (self::alreadyHas($powers, $powerName)) {
                    continue;
                }
                $powers[] = new Power($powerName, $tier);
            }
        }

        // Step 8: Spark Signature inputs.
        $tiersDesc = array_values($domainTiers);
        rsort($tiersDesc);
        $highestTier = $tiersDesc[0] ?? 1;
        $secondTier = $tiersDesc[1] ?? 0;
        $signature = self::sparkSignature($highestTier, $secondTier, count($powers));

        // Step 9: Combat Capability.
        $cc = CombatCapability::read($powers)->value();

        return new Sheet($maskName, $powers, $cc, $signature, $cc >= 100);
    }

    /**
     * Step 6/7: pick the powers that manifest within one domain.
     *
     * @param array<string,int> $vector powerName => flavorScore
     * @return string[]
     */
    private static function powersForDomain(string $domain, array $vector): array
    {
        $valid = [];
        foreach ($vector as $power => $score) {
            if (!DomainRegistry::containsPower($domain, $power)) {
                throw new \InvalidArgumentException("{$power} is not a {$domain} power.");
            }
            $valid[$power] = $score;
        }

        $manifested = [];
        foreach ($valid as $power => $score) {
            if ($score >= self::MANIFEST_FLAVOR_THRESHOLD) {
                $manifested[] = $power;
            }
        }

        // Step 7: latent-domain fallback. If nothing reached the threshold,
        // the domain still manifests its single best sub-affinity. We pick the
        // highest-scoring one; ties resolve to declaration order (a facilitator
        // judgment call in the rulebook — surfaced here as a deterministic pick).
        if ($manifested === [] && $valid !== []) {
            arsort($valid);
            $manifested[] = array_key_first($valid);
        }

        return $manifested;
    }

    /**
     * @param Power[] $powers
     */
    private static function alreadyHas(array $powers, string $name): bool
    {
        foreach ($powers as $p) {
            if ($p->name() === $name) {
                return true;
            }
        }
        return false;
    }
}
