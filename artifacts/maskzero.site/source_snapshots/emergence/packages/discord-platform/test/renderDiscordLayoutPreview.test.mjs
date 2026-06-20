import assert from "node:assert/strict";
import test from "node:test";

import { buildCampaignDiscordPlan } from "../src/campaignDiscordPlanner.mjs";
import { renderDiscordLayoutPreview } from "../src/renderDiscordLayoutPreview.mjs";

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

test("renders initial discord layout preview from governed plan", () => {
  const plan = buildCampaignDiscordPlan({ campaign });
  const md = renderDiscordLayoutPreview(plan);

  assert.equal(md.includes("# Discord Campaign Layout Preview"), true);
  assert.equal(md.includes("the-emergence-first-signal-table"), true);
  assert.equal(md.includes("the-emergence-first-signal-character-sheets"), true);
  assert.equal(md.includes("architect-sheet-thread"), true);
  assert.equal(md.includes("the-emergence-first-signal-battle-log"), true);
  assert.equal(md.includes("the-emergence-first-signal-recaps"), true);
  assert.equal(md.includes("the-emergence-first-signal-gm-notes"), true);
  assert.equal(md.includes("the-emergence-first-signal-voice"), true);
  assert.equal(md.includes("This preview does not mutate Discord."), true);
});
