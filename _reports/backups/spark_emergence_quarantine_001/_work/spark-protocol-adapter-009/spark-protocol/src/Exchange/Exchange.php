<?php

declare(strict_types=1);

namespace Spark\Exchange;

use Spark\Model\Power;
use Spark\Model\PowerRegistry;
use Spark\Model\Sheet;

/**
 * The Exchange — the one sanctioned way a locked sheet changes between
 * engagements. It never recomputes CC; it takes the locked CC as given
 * and ADDS the published capacity cost, capping at 100 (MAXED).
 *
 * Procedure (from Exchange_ShopV2):
 *   1. spare = 100 - CC
 *   2. look up capacity cost
 *   3. if cost > spare -> REFUSE
 *   4. else apply change, new CC = old CC + cost, re-derive Threat Class
 */
final class Exchange
{
    /**
     * Buy a brand-new power at a given tier.
     */
    public function buyNewPower(Sheet $sheet, string $powerName, int $tier): RequisitionResult
    {
        if (!PowerRegistry::isKnown($powerName)) {
            throw new \InvalidArgumentException("Unknown power: {$powerName}");
        }
        if ($sheet->findPower($powerName) !== null) {
            throw new \InvalidArgumentException(
                "Sheet already has {$powerName}; use upgradePower to raise its tier."
            );
        }

        $weight = PowerRegistry::getWeight($powerName);
        $cost = CapacityCost::newPower($weight, $tier);

        $this->guardCapacity($sheet, $cost, "new {$powerName} T{$tier}");

        $powers = $sheet->powers();
        $powers[] = new Power($powerName, $tier);

        return $this->relock(
            $sheet,
            $powers,
            $cost,
            "Requisition: new {$powerName} at Tier {$tier} ({$cost} capacity)"
        );
    }

    /**
     * Upgrade an existing power to a higher tier.
     */
    public function upgradePower(Sheet $sheet, string $powerName, int $targetTier): RequisitionResult
    {
        $existing = $sheet->findPower($powerName);
        if ($existing === null) {
            throw new \InvalidArgumentException("Sheet does not have {$powerName}.");
        }

        // Ceiling refusal must happen before semantic upgrade validation:
        // once the asset is MAXED / spare is zero, the Exchange shelf is closed.
        if ($sheet->isMaxed() || $sheet->spareCapacity() <= 0) {
            throw new RequisitionRefused(
                "Asset is MAXED at ceiling; the shelf is closed. Cannot buy {$powerName} ->T{$targetTier}."
            );
        }

        if ($targetTier <= $existing->tier()) {
            throw new \InvalidArgumentException(
                "Target tier {$targetTier} is not above current tier {$existing->tier()}."
            );
        }

        $weight = PowerRegistry::getWeight($powerName);

        // Upgrading is priced as the difference between owning the higher tier
        // and the lower one — i.e. the sum of the advance steps crossed.
        $cost = 0;
        for ($t = $existing->tier() + 1; $t <= $targetTier; $t++) {
            $cost += CapacityCost::advance($weight, $t);
        }

        $this->guardCapacity($sheet, $cost, "{$powerName} ->T{$targetTier}");

        $powers = [];
        foreach ($sheet->powers() as $p) {
            $powers[] = ($p->name() === $powerName) ? $p->withTier($targetTier) : $p;
        }

        return $this->relock(
            $sheet,
            $powers,
            $cost,
            "Requisition: {$powerName} raised to Tier {$targetTier} ({$cost} capacity)"
        );
    }

    private function guardCapacity(Sheet $sheet, int $cost, string $what): void
    {
        $spare = $sheet->spareCapacity();
        if ($sheet->isMaxed() || $spare <= 0) {
            throw new RequisitionRefused(
                "Asset is MAXED at ceiling; the shelf is closed. Cannot buy {$what}."
            );
        }
        if ($cost > $spare) {
            throw new RequisitionRefused(
                "Requisition refused: {$what} costs {$cost} capacity but only {$spare} spare remains."
            );
        }
    }

    /**
     * @param Power[] $powers
     */
    private function relock(Sheet $sheet, array $powers, int $cost, string $desc): RequisitionResult
    {
        $newCc = min(100, $sheet->combatCapability() + $cost);
        $maxed = ($newCc >= 100);
        $newSheet = $sheet->relock($powers, $newCc, $maxed);

        return new RequisitionResult($newSheet, $cost, $desc);
    }
}
