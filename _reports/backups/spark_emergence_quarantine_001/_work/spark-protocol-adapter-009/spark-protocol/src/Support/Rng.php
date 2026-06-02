<?php

declare(strict_types=1);

namespace Spark\Support;

/**
 * Source of binding 1..100 rolls. Abstracted so the outcome lottery can
 * be tested deterministically while production uses a real PRNG. This is
 * the code analogue of the protocol's "ALL ROLLS MUST BE REAL" rule:
 * the number is handed back by the RNG, never typed in by the caller.
 */
interface Rng
{
    /** Inclusive 1..max roll. */
    public function roll(int $max = 100): int;
}
