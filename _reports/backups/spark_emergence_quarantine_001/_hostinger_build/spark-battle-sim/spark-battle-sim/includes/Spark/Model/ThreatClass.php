<?php

declare(strict_types=1);

namespace Spark\Model;

/**
 * Step 10 Threat Class, keyed to Combat Capability bands.
 */
final class ThreatClass
{
    public const ALPHA = 'Alpha';
    public const BETA  = 'Beta';
    public const GAMMA = 'Gamma';
    public const DELTA = 'Delta';
    public const SIGMA = 'Sigma';

    /**
     * Map a Combat Capability (0-100) to its band.
     */
    public static function fromCC(int $cc): string
    {
        if ($cc < 0 || $cc > 100) {
            throw new \InvalidArgumentException("CC must be 0-100, got {$cc}");
        }
        if ($cc <= 15) {
            return self::ALPHA;
        }
        if ($cc <= 30) {
            return self::BETA;
        }
        if ($cc <= 50) {
            return self::GAMMA;
        }
        if ($cc <= 75) {
            return self::DELTA;
        }
        return self::SIGMA;
    }
}
