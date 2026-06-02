<?php

declare(strict_types=1);

namespace Spark\Model;

/**
 * An immutable manifested power on a locked sheet.
 *
 * Weight is derived from the registry, never passed in — the protocol
 * fixes weight by power name, so accepting it as a constructor argument
 * would let a sheet contradict the rulebook.
 */
final class Power
{
    /** @var string */
    private $name;

    /** @var int 1-5 */
    private $tier;

    /** @var string|null Healing Factor expression: Self-Directed / Other-Directed. */
    private $healingExpression;

    /** @var string|null Density Control mode: HEAVY / LIGHT (null if not Density). */
    private $densityMode;

    public function __construct(string $name, int $tier, ?string $healingExpression = null, ?string $densityMode = null)
    {
        if (!PowerRegistry::isKnown($name)) {
            throw new \InvalidArgumentException("Unknown power: {$name}");
        }
        if ($tier < 1 || $tier > 5) {
            throw new \InvalidArgumentException("Tier must be 1-5, got {$tier}");
        }
        $this->name = $name;
        $this->tier = $tier;
        $this->healingExpression = $healingExpression;
        $this->densityMode = $densityMode;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function tier(): int
    {
        return $this->tier;
    }

    public function weight(): string
    {
        return PowerRegistry::getWeight($this->name);
    }

    public function role(): string
    {
        return PowerRegistry::getRole($this->name);
    }

    public function axis(): ?string
    {
        return PowerRegistry::getAxis($this->name);
    }

    public function isStrategic(): bool
    {
        return PowerRegistry::isStrategic($this->name);
    }

    public function healingExpression(): ?string
    {
        return $this->healingExpression;
    }

    public function densityMode(): ?string
    {
        return $this->densityMode;
    }

    /** Spine slot value = tier * scaled multiplier (unrounded). */
    public function spineValueRaw(): float
    {
        return $this->tier * PowerRegistry::scaledMultiplier($this->name, $this->tier);
    }

    /** Bench-grid contribution. */
    public function benchValue(): int
    {
        return PowerRegistry::benchValue($this->name, $this->tier);
    }

    /** Return a copy at a new tier (used by the Exchange). */
    public function withTier(int $tier): self
    {
        return new self($this->name, $tier, $this->healingExpression, $this->densityMode);
    }
}
