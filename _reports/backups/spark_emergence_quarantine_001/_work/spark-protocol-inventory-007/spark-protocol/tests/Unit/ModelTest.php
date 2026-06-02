<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Model\Power;
use Spark\Model\PowerRegistry;
use Spark\Model\Sheet;
use Spark\Model\ThreatClass;
use Spark\Tests\TestCase;

final class ModelTest extends TestCase
{
    /** Step 10 band boundaries. */
    public function testThreatClassBands(): void
    {
        $this->assertSame('Alpha', ThreatClass::fromCC(0));
        $this->assertSame('Alpha', ThreatClass::fromCC(15));
        $this->assertSame('Beta', ThreatClass::fromCC(16));
        $this->assertSame('Beta', ThreatClass::fromCC(30));
        $this->assertSame('Gamma', ThreatClass::fromCC(31));
        $this->assertSame('Gamma', ThreatClass::fromCC(50));
        $this->assertSame('Delta', ThreatClass::fromCC(51));
        $this->assertSame('Delta', ThreatClass::fromCC(75));
        $this->assertSame('Sigma', ThreatClass::fromCC(76));
        $this->assertSame('Sigma', ThreatClass::fromCC(100));
    }

    public function testSpareCapacity(): void
    {
        $sheet = new Sheet('X', [new Power('Super Strength', 5)], 91);
        $this->assertSame(9, $sheet->spareCapacity());
    }

    public function testStrategicTagsDetected(): void
    {
        $sheet = new Sheet('Y', [
            new Power('Super Strength', 4),
            new Power('Healing Factor', 4),
            new Power('Mind Control', 3),
        ], 70);
        $tags = $sheet->strategicTags();
        $this->assertContains('Healing Factor', $tags);
        $this->assertContains('Mind Control', $tags);
        $this->assertFalse(in_array('Super Strength', $tags, true));
    }

    /** All 48 registry powers classify into exactly one weight. */
    public function testAllPowersHaveWeight(): void
    {
        foreach (PowerRegistry::all() as $power) {
            $w = PowerRegistry::getWeight($power);
            $this->assertContains($w, ['FULL', 'HALF', 'QUARTER']);
        }
    }

    public function testUnknownPowerRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Power('Heat Vision', 3);
    }

    public function testTierOutOfRangeRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Power('Super Strength', 6);
    }

    public function testWithTierReturnsCopy(): void
    {
        $p = new Power('Super Strength', 3);
        $up = $p->withTier(5);
        $this->assertSame(3, $p->tier());
        $this->assertSame(5, $up->tier());
    }
}
