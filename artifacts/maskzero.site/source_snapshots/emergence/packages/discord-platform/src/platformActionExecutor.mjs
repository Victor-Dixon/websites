import fs from "node:fs";
import path from "node:path";

export function buildExecutionManifest({
  plan,
  mode = "dry_run",
  generatedAt = new Date().toISOString()
}) {
  if (!plan || !Array.isArray(plan.actions)) {
    throw new Error("Discord platform plan with actions[] is required");
  }

  const allowed = plan.actions.filter((entry) => entry.decision?.allowed);
  const blocked = plan.actions.filter((entry) => !entry.decision?.allowed);

  return {
    schemaVersion: 1,
    type: "discord_platform_execution_manifest",
    mode,
    generatedAt,
    campaignId: plan.campaignId,
    title: plan.title,
    blockedCount: blocked.length,
    actions: allowed.map((entry, index) => ({
      index,
      status: "pending",
      dryRun: mode !== "live",
      action: entry.action,
      decision: entry.decision
    })),
    blocked: blocked.map((entry) => ({
      action: entry.action,
      decision: entry.decision
    }))
  };
}

export function assertLiveExecutionAllowed({
  mode,
  env = process.env
}) {
  if (mode !== "live") return true;

  if (env.DISCORD_PLATFORM_LIVE !== "1") {
    throw new Error("Live Discord execution requires DISCORD_PLATFORM_LIVE=1");
  }

  if (!env.DISCORD_BOT_TOKEN) {
    throw new Error("Live Discord execution requires DISCORD_BOT_TOKEN");
  }

  if (!env.DISCORD_GUILD_ID) {
    throw new Error("Live Discord execution requires DISCORD_GUILD_ID");
  }

  return true;
}

export function executeDiscordPlatformManifest({
  manifest,
  mode = manifest.mode || "dry_run",
  env = process.env,
  adapter = null,
  executedAt = new Date().toISOString()
}) {
  assertLiveExecutionAllowed({ mode, env });

  const audit = {
    schemaVersion: 1,
    type: "discord_platform_execution_audit",
    campaignId: manifest.campaignId,
    title: manifest.title,
    mode,
    executedAt,
    results: []
  };

  for (const item of manifest.actions) {
    if (mode !== "live") {
      audit.results.push({
        index: item.index,
        status: "dry_run_skipped",
        action: item.action,
        reason: "Dry-run mode does not mutate Discord"
      });
      continue;
    }

    if (!adapter || typeof adapter.execute !== "function") {
      throw new Error("Live execution requires adapter.execute(action)");
    }

    const result = adapter.execute(item.action);
    audit.results.push({
      index: item.index,
      status: "executed",
      action: item.action,
      result
    });
  }

  return audit;
}

export function saveExecutionArtifacts({
  manifest,
  audit,
  rootDir = "runtime/reports",
  basename = "discord_platform_action_executor_014"
}) {
  fs.mkdirSync(rootDir, { recursive: true });

  const manifestFile = path.join(rootDir, `${basename}_manifest.json`);
  const auditFile = path.join(rootDir, `${basename}_audit.json`);

  fs.writeFileSync(manifestFile, JSON.stringify(manifest, null, 2) + "\n");
  fs.writeFileSync(auditFile, JSON.stringify(audit, null, 2) + "\n");

  return {
    ok: true,
    manifestFile,
    auditFile
  };
}
