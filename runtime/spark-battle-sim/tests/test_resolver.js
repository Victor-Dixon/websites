const assert = require("assert");
const { resolveBattle, parseCC } = require("../server/battleResolver");

function fixedRng(values) {
  let i = 0;
  return () => values[i++ % values.length];
}

assert.strictEqual(parseCC("THE VICTOR — CC: 66, Threat: Delta"), 66);
assert.strictEqual(parseCC("No score here"), 50);

const result = resolveBattle({
  fighter1: "The Victor",
  fighter2: "Solomon Evil",
  sheet1: "THE VICTOR — CC: 66\nPOWERS: Flight T4, Super Speed T4",
  sheet2: "SOLOMON EVIL — CC: 85\nPOWERS: Super Strength T4, Invulnerability T4",
  rng: fixedRng([0.1, 0.2, 0.3, 0.4, 0.1, 0.7, 0.9, 0.99])
});

assert.ok(result.private);
assert.ok(result.arena.location);
assert.ok(result.winner);
assert.ok(result.loser);
assert.ok(["stomp", "favored", "competitive", "tossup"].includes(result.closeness));
assert.ok(result.notes.roll >= 1 && result.notes.roll <= 100);

console.log("SPARK_BATTLE_RESOLVER_TEST=PASS");
