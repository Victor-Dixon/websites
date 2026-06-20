import { buildCharacterFromQuiz } from "../../packages/character-engine/src/scoring.mjs";

const character = buildCharacterFromQuiz({
  codename: "Architect",
  domainScores: {
    Titan: 0,
    Velocity: 0,
    Energy: 0,
    Specter: 0,
    Omni: 0,
    Primal: 0,
    Mind: 35
  },
  flavorScores: {
    Mind: [3, 3, 0, 0, 0, 0]
  }
});

console.log(JSON.stringify(character, null, 2));
