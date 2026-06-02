<?php

declare(strict_types=1);

namespace Spark\Tests\Unit;

use Spark\Engine\DomainScores;
use Spark\Engine\SheetGenerator;
use Spark\Tests\TestCase;

/**
 * Protocol Steps 1-11 sheet generation. Worked values transcribed from
 * the rulebook (tier map, 25% gate, Spark Signature formula, Cast word).
 */
final class SheetGeneratorTest extends TestCase
{
    /** Step 2 tier map boundaries. */
    public function testTierMapping(): void
    {
        $this->assertSame(1, SheetGenerator::scoreToTier(0));
        $this->assertSame(1, SheetGenerator::scoreToTier(5));
        $this->assertSame(2, SheetGenerator::scoreToTier(6));
        $this->assertSame(2, SheetGenerator::scoreToTier(12));
        $this->assertSame(3, SheetGenerator::scoreToTier(13));
        $this->assertSame(3, SheetGenerator::scoreToTier(18));
        $this->assertSame(4, SheetGenerator::scoreToTier(19));
        $this->assertSame(4, SheetGenerator::scoreToTier(27));
        $this->assertSame(5, SheetGenerator::scoreToTier(28));
        $this->assertSame(5, SheetGenerator::scoreToTier(31));
    }

    /** Step 3: highest domain always manifests; others need >= 25%. */
    public function testManifestationGate(): void
    {
        // Highest = 20 -> threshold 5. Domains at/above 5 manifest.
        $scores = new DomainScores([
            'Titan' => 20, 'Velocity' => 5, 'Energy' => 4,
            'Specter' => 0, 'Duality' => 0, 'Omni' => 0,
            'Primal' => 0, 'Mind' => 0,
        ]);
        $manifested = SheetGenerator::manifestedDomains($scores);
        $this->assertContains('Titan', $manifested);
        $this->assertContains('Velocity', $manifested); // exactly at threshold
        $this->assertFalse(in_array('Energy', $manifested, true)); // below 5
    }

    /** Step 8: solo Tier-4 sheet, one power -> Spark Signature 81 (the floor). */
    public function testSparkSignatureSoloFloor(): void
    {
        // 70 + (4*2.5) + (0*1) + 1 power = 70 + 10 + 0 + 1 = 81.
        $sig = SheetGenerator::sparkSignature(4, 0, 1);
        $this->assertSame(81, $sig);
    }

    /** Step 8: two-domain example. */
    public function testSparkSignatureTwoDomain(): void
    {
        // 70 + (5*2.5) + (4*1) + 3 powers = 70 + 12.5 + 4 + 3 = 89.5 -> 90.
        $sig = SheetGenerator::sparkSignature(5, 4, 3);
        $this->assertSame(90, $sig);
    }

    /** Step 8 Cast descriptor. */
    public function testCastDescriptor(): void
    {
        $this->assertSame('Singular', SheetGenerator::cast(1));
        $this->assertSame('Focused', SheetGenerator::cast(2));
        $this->assertSame('Versatile', SheetGenerator::cast(3));
        $this->assertSame('Manifold', SheetGenerator::cast(4));
        $this->assertSame('Manifold', SheetGenerator::cast(6));
    }

    /** End-to-end: a solo Titan build locks a coherent sheet. */
    public function testGenerateSoloTitanSheet(): void
    {
        $scores = new DomainScores([
            'Titan' => 28, 'Velocity' => 4, 'Energy' => 4, 'Specter' => 4,
            'Duality' => 4, 'Omni' => 4, 'Primal' => 4, 'Mind' => 0,
        ]);
        // Flavor: Titan with Super Strength lead (5) and Invulnerability (3).
        $flavor = [
            'Titan' => ['Super Strength' => 5, 'Invulnerability' => 3],
        ];
        $sheet = SheetGenerator::generate('Bulwark', $scores, $flavor);

        // Titan at 28 -> T5; both sub-affinities >= 2 manifest.
        $this->assertTrue($sheet->findPower('Super Strength') !== null);
        $this->assertTrue($sheet->findPower('Invulnerability') !== null);
        // CC: SS T5 attack 5 + Invuln T5? no — Invuln manifests at the domain
        // tier (T5) since powers take their domain's tier. Co-leads -> CC 100.
        $this->assertSame(100, $sheet->combatCapability());
        $this->assertSame('Sigma', $sheet->threatClass());
    }

    /** Powers below flavor threshold (score < 2) do not manifest. */
    public function testSubThresholdPowerLatent(): void
    {
        $scores = new DomainScores([
            'Energy' => 20, 'Titan' => 3, 'Velocity' => 3, 'Specter' => 3,
            'Duality' => 3, 'Omni' => 3, 'Primal' => 3, 'Mind' => 0,
        ]);
        $flavor = [
            'Energy' => ['Pyrokinesis' => 4, 'Cryokinesis' => 1], // Cryo < 2 latent
        ];
        $sheet = SheetGenerator::generate('Ember', $scores, $flavor);
        $this->assertTrue($sheet->findPower('Pyrokinesis') !== null);
        $this->assertTrue($sheet->findPower('Cryokinesis') === null);
    }
}
