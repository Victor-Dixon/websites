import fs from "node:fs";
import { buildCampaignDiscordPlan } from "../../packages/discord-platform/src/campaignDiscordPlanner.mjs";

const campaign = JSON.parse(
  fs.readFileSync("data/campaigns/the-emergence-demo.json", "utf8")
);

const plan = buildCampaignDiscordPlan({ campaign });

console.log(JSON.stringify(plan, null, 2));
