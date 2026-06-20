import assert from "node:assert/strict";
import test from "node:test";

import {
  domainTier,
  manifests,
  percentile,
  threatClassification,
  buildCharacterFromQuiz,
  remainingMaxPointsAfterTopScore,
  t5ForcesSoloManifestation
} from "../src/scoring.mjs";

test("domain tier thresholds are deterministic", () => {
  assert.equal(domainTier(35), "T5");
  assert.equal(domainTier(24), "T4");
  assert.equal(domainTier(16), "T3");
  assert.equal(domainTier(7), "T2");
  assert.equal(domainTier(2), "T1");
});

test("manifestation gate uses 25 percent rule", () => {
  assert.equal(manifests(8, 30), true);
  assert.equal(manifests(7, 30), false);
});



test("T5 solo math exposes remaining-point ceiling", () => {
  assert.equal(remainingMaxPointsAfterTopScore(35), 8);
  assert.equal(t5ForcesSoloManifestation(35), true);
  assert.equal(manifests(7, 30), false);
});

test("T5 top domain mathematically forces solo manifestation", () => {
  const result = buildCharacterFromQuiz({
    codename: "SoloT5",
    domainScores: {
      Titan: 35,
      Velocity: 8,
      Energy: 0,
      Specter: 0,
      Omni: 0,
      Primal: 0,
      Mind: 0
    },
    flavorScores: {
      Titan: [6, 0, 0, 0, 0, 0],
      Velocity: [6, 0, 0, 0, 0, 0]
    }
  });

  assert.equal(result.domains.length, 1);
  assert.equal(result.domains[0].name, "Titan");
  assert.equal(result.powers.length, 1);
  assert.equal(result.powers[0].domain, "Titan");
});

test("percentile calculation is deterministic", () => {
  assert.equal(
    percentile({
      highestTier: 5,
      secondTier: 3,
      manifestedPowerCount: 2
    }),
    88
  );
});

test("threat classification maps correctly", () => {
  assert.equal(threatClassification(90), "City-Wide Potential");
  assert.equal(threatClassification(84), "City Block-Level Potential");
  assert.equal(threatClassification(80), "Building-Level Potential");
});

test("buildCharacterFromQuiz creates deterministic character sheet", () => {
  const result = buildCharacterFromQuiz({
    codename: "Livewire",
    domainScores: {
      Titan: 4,
      Velocity: 8,
      Energy: 35,
      Specter: 2,
      Omni: 5,
      Primal: 1,
      Mind: 9
    },
    flavorScores: {
      Energy: [6, 0, 0, 0, 0, 0],
      Velocity: [4, 0, 0, 0, 0, 0],
      Mind: [0, 6, 0, 0, 0, 0]
    }
  });

  assert.equal(result.name, "Livewire");
  assert.equal(result.percentile, 84);
  assert.equal(result.domains.length, 1);
  assert.equal(result.powers.length, 1);
  assert.equal(result.domains.some((d) => d.name === "Mind"), false);
  assert.equal(result.threatTags.length, 0);
});
