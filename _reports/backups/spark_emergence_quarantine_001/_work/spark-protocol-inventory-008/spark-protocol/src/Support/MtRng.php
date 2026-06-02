<?php

declare(strict_types=1);

namespace Spark\Support;

/**
 * Production RNG backed by random_int (CSPRNG). Real rolls only.
 */
final class MtRng implements Rng
{
    public function roll(int $max = 100): int
    {
        return random_int(1, $max);
    }
}
