import { adjudicateBattle } from "../../packages/combat-engine/src/battleEngine.mjs";

const backstop = {
  name: "Backstop",
  powers: [
    { name: "Invulnerability", tier: "T5" },
    { name: "Super Strength", tier: "T5" }
  ],
  threatTags: []
};

const blink = {
  name: "Blink",
  powers: [
    { name: "Teleportation", tier: "T3" }
  ],
  threatTags: ["Teleportation"]
};

const result = adjudicateBattle({
  fighterA: backstop,
  fighterB: blink,
  arena: { name: "Rainy construction site", favors: "Blink" },
  seed: "demo-battle-001"
});

console.log(JSON.stringify(result, null, 2));
