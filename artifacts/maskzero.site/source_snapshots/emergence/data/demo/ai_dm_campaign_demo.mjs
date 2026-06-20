import {
  createCampaign,
  createScenePrompt,
  saveCampaign
} from "../../packages/campaign-engine/src/campaignEngine.mjs";

const campaign = createCampaign({
  campaignId: "the-emergence-demo",
  title: "The Emergence: First Signal",
  seed: "the-emergence-demo-seed",
  characterFiles: ["data/characters/100000000000002.json"],
  createdAt: "2026-05-24T00:00:00.000Z"
});

const saved = saveCampaign({ campaign });
const scenePrompt = createScenePrompt({
  campaign,
  playerIntent: "Architect investigates the first anomaly pulse"
});

console.log(JSON.stringify({
  saved,
  scenePrompt
}, null, 2));
