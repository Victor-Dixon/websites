<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Exchange\Exchange;
use Spark\Exchange\RequisitionRefused;
use Spark\Model\Power;
use Spark\Model\Sheet;
use Spark\Tests\TestCase;

final class ExchangeTest extends TestCase
{
    private function asset91(): Sheet
    {
        // Pyro T5 + Invuln T4 + Flight T1 -> CC 91, spare 9.
        return new Sheet('Example-91', [
            new Power('Pyrokinesis', 5),
            new Power('Invulnerability', 4),
            new Power('Flight', 1),
        ], 91);
    }

    /** Anchor 1: a new T3 power spends all 9 spare -> CC 100, MAXED. */
    public function testAnchor1NewT3Maxes(): void
    {
        $ex = new Exchange();
        $result = $ex->buyNewPower($this->asset91(), 'Concussive Blasts', 3);
        $this->assertSame(100, $result->sheet()->combatCapability());
        $this->assertTrue($result->sheet()->isMaxed());
        $this->assertSame(9, $result->capacitySpent());
    }

    /** Anchor 1: a FULL ->T4 upgrade also spends exactly 9 -> CC 100. */
    public function testAnchor1UpgradeToT4Maxes(): void
    {
        $ex = new Exchange();
        // Upgrade the Invulnerability T4? it's already T4; upgrade Pyro is T5.
        // Use a fresh full-weight power at T3 to upgrade to T4.
        $sheet = new Sheet('Up', [
            new Power('Pyrokinesis', 5),
            new Power('Concussive Blasts', 3), // full, T3 -> T4 costs 9
        ], 91);
        $result = $ex->upgradePower($sheet, 'Concussive Blasts', 4);
        $this->assertSame(100, $result->sheet()->combatCapability());
        $this->assertSame(9, $result->capacitySpent());
    }

    /** Anchor 1: the 91-asset cannot take BOTH buys — second is refused. */
    public function testAnchor1CannotTakeBoth(): void
    {
        $ex = new Exchange();
        $after = $ex->buyNewPower($this->asset91(), 'Concussive Blasts', 3)->sheet();
        // Now spare = 0; any further purchase refused.
        $this->expectException(RequisitionRefused::class);
        $ex->upgradePower($after, 'Pyrokinesis', 5); // already T5 anyway, but ceiling first
    }

    /** Over-capacity purchase is refused, never silently clamped. */
    public function testOverCapacityRefused(): void
    {
        $ex = new Exchange();
        $sheet = new Sheet('Tight', [new Power('Super Strength', 5)], 95); // spare 5
        $this->expectException(RequisitionRefused::class);
        $ex->buyNewPower($sheet, 'Pyrokinesis', 4); // FULL new T4 = 18 > 5
    }

    /** A legal half-weight ->T4 (cost 6) on the 91 leaves spare and does NOT max. */
    public function testHalfUpgradeLeavesSpare(): void
    {
        $ex = new Exchange();
        $sheet = new Sheet('HalfUp', [
            new Power('Pyrokinesis', 5),
            new Power('Force Fields', 3), // HALF T3 -> T4 costs 6
        ], 91);
        // spare is 9; 6 <= 9 so legal, leaving 3 spare, CC 97, not maxed.
        $result = $ex->upgradePower($sheet, 'Force Fields', 4);
        $this->assertSame(97, $result->sheet()->combatCapability());
        $this->assertFalse($result->sheet()->isMaxed());
    }

    /** Anchor 2: dual-T5 reads 100, spare 0; a third T5 is unbuyable. */
    public function testAnchor2ThirdT5Unbuyable(): void
    {
        $ex = new Exchange();
        $dualT5 = new Sheet('Ceiling', [
            new Power('Super Strength', 5),
            new Power('Invulnerability', 5),
        ], 100);
        $this->expectException(RequisitionRefused::class);
        $ex->buyNewPower($dualT5, 'Pyrokinesis', 5);
    }

    /** Buying a power applies the tier and re-derives Threat Class. */
    public function testThreatClassReDerivedAfterBuy(): void
    {
        $ex = new Exchange();
        $sheet = new Sheet('Climber', [new Power('Super Strength', 5)], 50); // Gamma
        $result = $ex->buyNewPower($sheet, 'Invulnerability', 4); // +? FULL new T4 = 18 -> CC 68 Delta
        $this->assertSame(68, $result->sheet()->combatCapability());
        $this->assertSame('Delta', $result->sheet()->threatClass());
    }

    /** The new power actually appears on the re-locked sheet. */
    public function testNewPowerOnSheet(): void
    {
        $ex = new Exchange();
        $sheet = new Sheet('Adder', [new Power('Super Strength', 3)], 30);
        $result = $ex->buyNewPower($sheet, 'Flight', 2); // HALF new T2 = 3 -> CC 33
        $this->assertSame(33, $result->sheet()->combatCapability());
        $this->assertTrue($result->sheet()->findPower('Flight') !== null);
    }

    /** Upgrading a power the sheet does not have is rejected. */
    public function testUpgradeMissingPowerRejected(): void
    {
        $ex = new Exchange();
        $sheet = new Sheet('NoPow', [new Power('Super Strength', 3)], 30);
        $this->expectException(\InvalidArgumentException::class);
        $ex->upgradePower($sheet, 'Pyrokinesis', 4);
    }
}
