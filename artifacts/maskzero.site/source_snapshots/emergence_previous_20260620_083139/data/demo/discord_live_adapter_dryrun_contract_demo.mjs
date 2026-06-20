import fs from "node:fs";
import { createMockDiscordRestAdapter } from "../../packages/discord-platform/src/discordRestAdapter.mjs";
import { executeDiscordPlatformManifest } from "../../packages/discord-platform/src/platformActionExecutor.mjs";

const manifest = JSON.parse(
  fs.readFileSync("runtime/reports/discord_platform_action_executor_014_manifest.json", "utf8")
);

const dryRunAudit = executeDiscordPlatformManifest({
  manifest,
  mode: "dry_run",
  executedAt: "2026-05-24T00:00:00.000Z",
  adapter: createMockDiscordRestAdapter()
});

console.log(JSON.stringify({
  mode: dryRunAudit.mode,
  results: dryRunAudit.results.length,
  statuses: [...new Set(dryRunAudit.results.map((entry) => entry.status))],
  networkMutation: false
}, null, 2));
