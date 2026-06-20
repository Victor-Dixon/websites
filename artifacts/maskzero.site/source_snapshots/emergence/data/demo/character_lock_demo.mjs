import { saveLockedCharacter, loadLockedCharacter } from "../../packages/character-engine/src/characterStore.mjs";

const sheet = {
  name: "Livewire",
  percentile: 89,
  threatClassification: "City-Wide Potential",
  domains: [{ name: "Energy", tier: "T5", score: 35 }],
  powers: [{ name: "Electrokinesis", tier: "T5" }],
  threatTags: []
};

const saved = saveLockedCharacter({
  discordUserId: "100000000000001",
  discordUsername: "demo_user",
  quizVersion: "spark-protocol-v5",
  sheet,
  now: "2026-05-24T00:00:00.000Z"
});

const loaded = loadLockedCharacter({
  discordUserId: "100000000000001"
});

console.log(JSON.stringify({ saved, loaded }, null, 2));
