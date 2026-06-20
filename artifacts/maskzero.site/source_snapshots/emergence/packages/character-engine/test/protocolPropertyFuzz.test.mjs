import assert from "node:assert/strict";
import test from "node:test";

import { DOMAINS } from "../src/powerRegistry.mjs";
import {
  buildCharacterFromQuiz,
  domainTier,
  manifestationThreshold,
  remainingMaxPointsAfterTopScore,
  t5ForcesSoloManifestation,
  tierNumber
} from "../src/scoring.mjs";

function seededRng(seed) {
  let x = seed >>> 0;
  return () => {
    x ^= x << 13;
    x ^= x >>> 17;
    x ^= x << 5;
    return ((x >>> 0) / 4294967296);
  };
}

function randomInt(rng, min, max) {
  return min + Math.floor(rng() * (max - min + 1));
}

function randomFlavorScores(rng) {
  const scores = [0, 0, 0, 0, 0, 0];

  for (let i = 0; i < 6; i += 1) {
    scores[randomInt(rng, 0, 5)] += 1;
  }

  return scores;
}

function randomDomainScores(rng) {
  const scores = Object.fromEntries(DOMAINS.map((domain) => [domain, 0]));

  // Approximate valid 36-question / 0-2 point allocation.
  // This keeps total <= 72 and each domain <= 39.
  for (let i = 0; i < 36; i += 1) {
    const domain = DOMAINS[randomInt(rng, 0, DOMAINS.length - 1)];
    const points = rng() < 0.12 ? 2 : 1;
    scores[domain] = Math.min(39, scores[domain] + points);
  }

  return scores;
}

function flavorScoresForManifestedDomains(rng, domainScores) {
  const highest = Math.max(...Object.values(domainScores));
  const flavorScores = {};

  for (const domain of DOMAINS) {
    const score = domainScores[domain];
    const manifests = t5ForcesSoloManifestation(highest)
      ? score === highest
      : score >= manifestationThreshold(highest);

    if (manifests) {
      flavorScores[domain] = randomFlavorScores(rng);
    }
  }

  return flavorScores;
}

function expectedPercentile(character) {
  const tiers = character.domains
    .map((domain) => tierNumber(domain.tier))
    .sort((a, b) => b - a);

  return Math.round(
    70 +
    ((tiers[0] || 1) * 2.5) +
    ((tiers[1] || 0) * 1) +
    character.powers.length
  );
}

test("T5 top score always mathematically forces solo manifestation for scores 35 through 39", () => {
  for (let topScore = 35; topScore <= 39; topScore += 1) {
    assert.equal(t5ForcesSoloManifestation(topScore), true);
    assert.equal(remainingMaxPointsAfterTopScore(topScore) < manifestationThreshold(topScore), true);
  }
});

test("T5 generated sheets never manifest second domains", () => {
  for (let topScore = 35; topScore <= 39; topScore += 1) {
    for (const topDomain of DOMAINS) {
      const domainScores = Object.fromEntries(DOMAINS.map((domain) => [domain, 0]));
      domainScores[topDomain] = topScore;

      // Set every non-top domain as high as possible without violating the T5 leftover ceiling.
      for (const domain of DOMAINS) {
        if (domain !== topDomain) {
          domainScores[domain] = 8;
        }
      }

      const character = buildCharacterFromQuiz({
        codename: `Solo-${topDomain}-${topScore}`,
        domainScores,
        flavorScores: {
          [topDomain]: [3, 3, 0, 0, 0, 0]
        }
      });

      assert.equal(character.domains.length, 1);
      assert.equal(character.domains[0].name, topDomain);
      assert.equal(character.domains[0].tier, "T5");
    }
  }
});

test("random generated sheets preserve percentile formula and tier bounds", () => {
  const rng = seededRng(0xC0FFEE);

  for (let i = 0; i < 500; i += 1) {
    const domainScores = randomDomainScores(rng);
    const flavorScores = flavorScoresForManifestedDomains(rng, domainScores);

    const character = buildCharacterFromQuiz({
      codename: `Fuzz-${i}`,
      domainScores,
      flavorScores
    });

    assert.equal(character.percentile, expectedPercentile(character));
    assert.equal(character.percentile >= 73, true);
    assert.equal(character.percentile <= 95, true);

    for (const domain of character.domains) {
      assert.equal(domain.tier, domainTier(domain.score));
    }

    for (const power of character.powers) {
      const domain = character.domains.find((entry) => entry.name === power.domain);
      assert.ok(domain, `Power ${power.name} references manifested domain ${power.domain}`);
      assert.equal(tierNumber(power.tier) <= tierNumber(domain.tier), true);
    }

    assert.equal(character.powers.length <= character.domains.length * 6, true);
  }
});

test("invalid domain scores are rejected", () => {
  const domainScores = Object.fromEntries(DOMAINS.map((domain) => [domain, 0]));
  domainScores.Mind = 40;

  assert.throws(
    () => buildCharacterFromQuiz({
      codename: "InvalidDomainScore",
      domainScores,
      flavorScores: {
        Mind: [6, 0, 0, 0, 0, 0]
      }
    }),
    /domain score must be between 0 and 39/
  );
});

test("missing flavor score for manifested domain is rejected", () => {
  const domainScores = Object.fromEntries(DOMAINS.map((domain) => [domain, 0]));
  domainScores.Mind = 24;

  assert.throws(
    () => buildCharacterFromQuiz({
      codename: "MissingFlavor",
      domainScores,
      flavorScores: {}
    }),
    /Missing flavor scores/
  );
});

test("flavor scores with more or less than six total points are rejected", () => {
  const domainScores = Object.fromEntries(DOMAINS.map((domain) => [domain, 0]));
  domainScores.Mind = 24;

  assert.throws(
    () => buildCharacterFromQuiz({
      codename: "TooManyFlavorPoints",
      domainScores,
      flavorScores: {
        Mind: [6, 1, 0, 0, 0, 0]
      }
    }),
    /must total exactly 6/
  );

  assert.throws(
    () => buildCharacterFromQuiz({
      codename: "TooFewFlavorPoints",
      domainScores,
      flavorScores: {
        Mind: [5, 0, 0, 0, 0, 0]
      }
    }),
    /must total exactly 6/
  );
});
