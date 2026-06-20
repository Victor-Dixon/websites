import assert from "node:assert/strict";
import test from "node:test";

import {
  assertPlanHasNoBlockedActions,
  buildCampaignDiscordPlan
} from "../src/campaignDiscordPlanner.mjs";

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

test("builds dry-run discord platform plan for campaign", () => {
  const plan = buildCampaignDiscordPlan({ campaign });

  assert.equal(plan.mode, "dry_run");
  assert.equal(plan.summary.blocked, 0);
  assert.equal(plan.actions.some((entry) => entry.action.type === "create_campaign_category"), true);
  assert.equal(plan.actions.some((entry) => entry.action.type === "create_private_gm_channel"), true);
  assert.equal(assertPlanHasNoBlockedActions(plan), true);
});

test("planner includes character sheet thread", () => {
  const plan = buildCampaignDiscordPlan({ campaign });

  assert.equal(
    plan.actions.some((entry) => entry.action.name === "architect-sheet-thread"),
    true
  );
});
