<?php

declare(strict_types=1);

namespace Spark\Support;

/**
 * Deterministic RNG for tests: returns a pre-seeded sequence of rolls,
 * wrapping around if exhausted.
 */
final class SequenceRng implements Rng
{
    /** @var int[] */
    private $sequence;
    /** @var int */
    private $index = 0;

    /**
     * @param int[] $sequence
     */
    public function __construct(array $sequence)
    {
        if (count($sequence) === 0) {
            throw new \InvalidArgumentException('Sequence must not be empty.');
        }
        $this->sequence = array_values($sequence);
    }

    public function roll(int $max = 100): int
    {
        $value = $this->sequence[$this->index % count($this->sequence)];
        $this->index++;
        // Clamp into 1..max so a sequence reused across different maxima stays valid.
        return max(1, min($max, $value));
    }
}
