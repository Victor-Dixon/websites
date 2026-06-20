import assert from "node:assert/strict";
import test from "node:test";

import {
  validateFlavorScores
} from "../src/protocolValidator.mjs";

import {
  buildCharacterFromQuiz,
  resolveFlavorPowers
} from "../src/scoring.mjs";

const soloT5MindDomainScores = {
  Titan: 0,
  Velocity: 0,
  Energy: 0,
  Specter: 0,
  Omni: 0,
  Primal: 0,
  Mind: 35
};

test("rejects impossible flavor totals over six", () => {
  assert.throws(
    () => validateFlavorScores("Mind", [4, 4, 3, 2, 0, 0]),
    /must total exactly 6/
  );
});

test("accepts exactly six flavor points", () => {
  assert.equal(validateFlavorScores("Mind", [3, 3, 0, 0, 0, 0]), true);
});

test("co-primary flavor tie at top manifests both at full tier", () => {
  const powers = resolveFlavorPowers({
    domain: "Mind",
    domainTierLabel: "T5",
    flavorScores: [3, 3, 0, 0, 0, 0]
  });

  assert.equal(powers.length, 2);
  assert.deepEqual(powers.map((power) => power.name), ["Telepathy", "Mind Control"]);
  assert.equal(powers.every((power) => power.tier === "T5"), true);
  assert.equal(powers.every((power) => power.classification === "co-primary"), true);
});

test("primary plus secondary uses tier minus one", () => {
  const powers = resolveFlavorPowers({
    domain: "Mind",
    domainTierLabel: "T5",
    flavorScores: [4, 2, 0, 0, 0, 0]
  });

  assert.equal(powers.length, 2);
  assert.equal(powers[0].name, "Telepathy");
  assert.equal(powers[0].tier, "T5");
  assert.equal(powers[0].classification, "primary");
  assert.equal(powers[1].name, "Mind Control");
  assert.equal(powers[1].tier, "T4");
  assert.equal(powers[1].classification, "secondary");
});

test("three-way top tie creates three co-primaries with no depth", () => {
  const powers = resolveFlavorPowers({
    domain: "Mind",
    domainTierLabel: "T5",
    flavorScores: [2, 2, 2, 0, 0, 0]
  });

  assert.equal(powers.length, 3);
  assert.equal(powers.every((power) => power.tier === "T5"), true);
  assert.equal(powers.every((power) => power.classification === "co-primary"), true);
});

test("tier one domain gets exactly one primary and no depth", () => {
  const powers = resolveFlavorPowers({
    domain: "Mind",
    domainTierLabel: "T1",
    flavorScores: [2, 2, 2, 0, 0, 0]
  });

  assert.equal(powers.length, 1);
  assert.equal(powers[0].tier, "T1");
  assert.equal(powers[0].classification, "primary");
});

test("impossible flavor sheet is rejected before character generation", () => {
  assert.throws(
    () => buildCharacterFromQuiz({
      codename: "InvalidMind",
      domainScores: soloT5MindDomainScores,
      flavorScores: {
        Mind: [4, 4, 3, 2, 0, 0]
      }
    }),
    /must total exactly 6/
  );
});

test("valid simulated Victor test character stays protocol compliant", () => {
  const result = buildCharacterFromQuiz({
    codename: "Architect",
    domainScores: soloT5MindDomainScores,
    flavorScores: {
      Mind: [3, 3, 0, 0, 0, 0]
    }
  });

  assert.equal(result.domains.length, 1);
  assert.equal(result.domains[0].name, "Mind");
  assert.equal(result.domains[0].tier, "T5");
  assert.equal(result.powers.length, 2);
  assert.deepEqual(result.powers.map((power) => power.name), ["Telepathy", "Mind Control"]);
  assert.equal(result.percentile, 85);
  assert.deepEqual(result.threatTags, ["Mind Control"]);
});
