import fs from "node:fs";
import { buildCampaignDiscordPlan } from "../../packages/discord-platform/src/campaignDiscordPlanner.mjs";
import {
  buildExecutionManifest,
  executeDiscordPlatformManifest,
  saveExecutionArtifacts
} from "../../packages/discord-platform/src/platformActionExecutor.mjs";

const campaign = JSON.parse(
  fs.readFileSync("data/campaigns/the-emergence-demo.json", "utf8")
);

const plan = buildCampaignDiscordPlan({ campaign });
const manifest = buildExecutionManifest({
  plan,
  mode: "dry_run",
  generatedAt: "2026-05-24T00:00:00.000Z"
});

const audit = executeDiscordPlatformManifest({
  manifest,
  mode: "dry_run",
  executedAt: "2026-05-24T00:00:00.000Z"
});

const saved = saveExecutionArtifacts({
  manifest,
  audit
});

console.log(JSON.stringify({
  saved,
  manifestSummary: {
    campaignId: manifest.campaignId,
    mode: manifest.mode,
    actions: manifest.actions.length,
    blocked: manifest.blockedCount
  },
  auditSummary: {
    mode: audit.mode,
    results: audit.results.length,
    statuses: [...new Set(audit.results.map((entry) => entry.status))]
  }
}, null, 2));
