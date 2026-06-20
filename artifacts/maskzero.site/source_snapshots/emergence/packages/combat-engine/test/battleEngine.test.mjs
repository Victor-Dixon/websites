import assert from "node:assert/strict";
import test from "node:test";
import { createSeededRng } from "../src/rng.mjs";
import { tierInteraction } from "../src/tierMatrix.mjs";
import { calculateBattleOdds, adjudicateBattle } from "../src/battleEngine.mjs";

const titan = {
  name: "Backstop",
  powers: [{ name: "Invulnerability", tier: "T5" }, { name: "Super Strength", tier: "T5" }],
  threatTags: []
};

const speedster = {
  name: "Slipstream",
  powers: [{ name: "Super Speed", tier: "T3" }],
  threatTags: []
};

const teleporter = {
  name: "Blink",
  powers: [{ name: "Teleportation", tier: "T3" }],
  threatTags: ["Teleportation"]
};

test("seeded RNG is reproducible", () => {
  const a = createSeededRng("same-seed");
  const b = createSeededRng("same-seed");
  assert.equal(a(), b());
  assert.equal(a(), b());
});

test("tier matrix blocks impossible wounds across large gaps", () => {
  const result = tierInteraction("T3", "T5");
  assert.equal(result.effect, "ineffective");
  assert.equal(result.canWound, false);
});

test("higher tier fighter is favored but not guaranteed", () => {
  const result = calculateBattleOdds(titan, speedster);
  assert.equal(result.odds.Backstop > 0.5, true);
  assert.equal(result.odds.Backstop < 1, true);
});

test("strategic tags improve underdog odds", () => {
  const normal = calculateBattleOdds(titan, speedster);
  const tagged = calculateBattleOdds(titan, teleporter);
  assert.equal(tagged.odds.Backstop < normal.odds.Backstop, true);
});

test("battle adjudication is deterministic for same seed", () => {
  const a = adjudicateBattle({ fighterA: titan, fighterB: teleporter, seed: "battle-001" });
  const b = adjudicateBattle({ fighterA: titan, fighterB: teleporter, seed: "battle-001" });
  assert.deepEqual(a, b);
});
