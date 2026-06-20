import assert from "node:assert/strict";
import test from "node:test";

import { buildCampaignDiscordPlan } from "../src/campaignDiscordPlanner.mjs";
import {
  assertLiveExecutionAllowed,
  buildExecutionManifest,
  executeDiscordPlatformManifest
} from "../src/platformActionExecutor.mjs";

const campaign = {
  campaignId: "the-emergence-demo",
  title: "The Emergence: First Signal",
  party: [
    {
      discordUserId: "100000000000002",
      activeRole: "Architect",
      character: {
        name: "Architect",
        powers: [
          { name: "Telepathy", tier: "T5" },
          { name: "Mind Control", tier: "T5" }
        ]
      }
    }
  ]
};

test("builds execution manifest from governed plan", () => {
  const plan = buildCampaignDiscordPlan({ campaign });
  const manifest = buildExecutionManifest({
    plan,
    mode: "dry_run",
    generatedAt: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(manifest.type, "discord_platform_execution_manifest");
  assert.equal(manifest.mode, "dry_run");
  assert.equal(manifest.actions.length, 9);
  assert.equal(manifest.blockedCount, 0);
  assert.equal(manifest.actions.every((entry) => entry.dryRun === true), true);
});

test("dry-run execution never calls adapter", () => {
  const plan = buildCampaignDiscordPlan({ campaign });
  const manifest = buildExecutionManifest({ plan, mode: "dry_run" });

  let called = false;
  const audit = executeDiscordPlatformManifest({
    manifest,
    mode: "dry_run",
    adapter: {
      execute() {
        called = true;
      }
    },
    executedAt: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(called, false);
  assert.equal(audit.results.length, 9);
  assert.equal(audit.results.every((entry) => entry.status === "dry_run_skipped"), true);
});

test("live execution is blocked without explicit env gate", () => {
  assert.throws(
    () => assertLiveExecutionAllowed({
      mode: "live",
      env: {}
    }),
    /DISCORD_PLATFORM_LIVE=1/
  );
});

test("live execution requires bot token and guild id", () => {
  assert.throws(
    () => assertLiveExecutionAllowed({
      mode: "live",
      env: {
        DISCORD_PLATFORM_LIVE: "1"
      }
    }),
    /DISCORD_BOT_TOKEN/
  );

  assert.throws(
    () => assertLiveExecutionAllowed({
      mode: "live",
      env: {
        DISCORD_PLATFORM_LIVE: "1",
        DISCORD_BOT_TOKEN: "token"
      }
    }),
    /DISCORD_GUILD_ID/
  );
});

test("live execution calls adapter only when explicitly gated", () => {
  const plan = buildCampaignDiscordPlan({ campaign });
  const manifest = buildExecutionManifest({ plan, mode: "live" });

  const executed = [];
  const audit = executeDiscordPlatformManifest({
    manifest,
    mode: "live",
    env: {
      DISCORD_PLATFORM_LIVE: "1",
      DISCORD_BOT_TOKEN: "token",
      DISCORD_GUILD_ID: "guild"
    },
    adapter: {
      execute(action) {
        executed.push(action.type);
        return { ok: true, actionType: action.type };
      }
    },
    executedAt: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(executed.length, 9);
  assert.equal(audit.results.every((entry) => entry.status === "executed"), true);
});
