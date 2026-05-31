<?php

declare(strict_types=1);

namespace Spark\Model;

/**
 * A LOCKED character sheet — the frozen truth every subsystem reads.
 *
 * The simulator and dossier writer never recompute CC; they take the
 * locked value. The Exchange produces a *new* locked Sheet. So CC,
 * Threat Class, and tags are stored, not derived on read — though
 * {@see CombatCapability} can compute a fresh read when explicitly asked.
 */
final class Sheet
{
    /** @var string */
    private $maskName;

    /** @var Power[] */
    private $powers;

    /** @var int Locked Combat Capability (the Step 9 read at lock time). */
    private $combatCapability;

    /** @var int Spark Signature (psychology; never a fight stat). */
    private $sparkSignature;

    /** @var bool Whether this sheet is MAXED (Exchange ceiling exception). */
    private $maxed;

    /**
     * @param Power[] $powers
     */
    public function __construct(
        string $maskName,
        array $powers,
        int $combatCapability,
        int $sparkSignature = 81,
        bool $maxed = false
    ) {
        foreach ($powers as $p) {
            if (!$p instanceof Power) {
                throw new \InvalidArgumentException('powers must be Power[]');
            }
        }
        if ($combatCapability < 0 || $combatCapability > 100) {
            throw new \InvalidArgumentException("CC must be 0-100, got {$combatCapability}");
        }
        $this->maskName = $maskName;
        $this->powers = array_values($powers);
        $this->combatCapability = $combatCapability;
        $this->sparkSignature = $sparkSignature;
        $this->maxed = $maxed;
    }

    public function maskName(): string
    {
        return $this->maskName;
    }

    /** @return Power[] */
    public function powers(): array
    {
        return $this->powers;
    }

    public function combatCapability(): int
    {
        return $this->combatCapability;
    }

    public function sparkSignature(): int
    {
        return $this->sparkSignature;
    }

    public function isMaxed(): bool
    {
        return $this->maxed;
    }

    /** Spare capacity = 100 - CC (the Exchange's headroom). */
    public function spareCapacity(): int
    {
        return 100 - $this->combatCapability;
    }

    /** Step 10 Threat Class, always keyed to the locked CC. */
    public function threatClass(): string
    {
        return ThreatClass::fromCC($this->combatCapability);
    }

    /** Strategic-threat tags present on the sheet (Step 11). */
    public function strategicTags(): array
    {
        $tags = [];
        foreach ($this->powers as $p) {
            if ($p->isStrategic()) {
                $tags[] = $p->name();
            }
        }
        return array_values(array_unique($tags));
    }

    public function findPower(string $name): ?Power
    {
        foreach ($this->powers as $p) {
            if ($p->name() === $name) {
                return $p;
            }
        }
        return null;
    }

    /**
     * Return a new locked Sheet with an updated power set and CC.
     * Used by the Exchange to re-lock between engagements.
     *
     * @param Power[] $powers
     */
    public function relock(array $powers, int $combatCapability, bool $maxed = false): self
    {
        return new self($this->maskName, $powers, $combatCapability, $this->sparkSignature, $maxed);
    }
}
