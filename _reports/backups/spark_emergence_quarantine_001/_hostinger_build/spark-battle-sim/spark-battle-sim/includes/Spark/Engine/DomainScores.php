<?php

declare(strict_types=1);

namespace Spark\Engine;

use Spark\Model\DomainRegistry;

/**
 * The eight domain totals from Step 1 (each 0-31). Immutable.
 */
final class DomainScores
{
    /** @var array<string,int> */
    private $scores;

    /**
     * @param array<string,int> $scores
     */
    public function __construct(array $scores)
    {
        $normalized = [];
        foreach (DomainRegistry::DOMAINS as $domain) {
            $value = $scores[$domain] ?? 0;
            if ($value < 0 || $value > 31) {
                throw new \InvalidArgumentException("Domain {$domain} score must be 0-31, got {$value}");
            }
            $normalized[$domain] = $value;
        }
        $this->scores = $normalized;
    }

    public function get(string $domain): int
    {
        if (!isset($this->scores[$domain])) {
            throw new \InvalidArgumentException("Unknown domain: {$domain}");
        }
        return $this->scores[$domain];
    }

    /** @return array<string,int> */
    public function all(): array
    {
        return $this->scores;
    }

    public function highest(): int
    {
        return max($this->scores);
    }
}
