import fs from "node:fs";
import { buildCampaignDiscordPlan } from "../../packages/discord-platform/src/campaignDiscordPlanner.mjs";
import { renderDiscordLayoutPreview } from "../../packages/discord-platform/src/renderDiscordLayoutPreview.mjs";

const campaign = JSON.parse(
  fs.readFileSync("data/campaigns/the-emergence-demo.json", "utf8")
);

const plan = buildCampaignDiscordPlan({ campaign });
const markdown = renderDiscordLayoutPreview(plan);

fs.writeFileSync("runtime/reports/discord_initial_layout_showcase_011.md", markdown);
process.stdout.write(markdown);
