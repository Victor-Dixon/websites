import assert from "node:assert/strict";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";
import test from "node:test";

import {
  advanceCampaignScene,
  buildDmContextPacket,
  createCampaign,
  createScenePrompt,
  loadCampaign,
  saveCampaign
} from "../src/campaignEngine.mjs";

function tempRoot() {
  return fs.mkdtempSync(path.join(os.tmpdir(), "spark-campaign-"));
}

function writeLockedCharacter(rootDir) {
  const file = path.join(rootDir, "100000000000002.json");
  fs.writeFileSync(file, JSON.stringify({
    schemaVersion: 1,
    locked: true,
    discordUserId: "100000000000002",
    discordUsername: "victor_demo",
    quizVersion: "spark-protocol-v5",
    activeRole: "Architect",
    character: {
      name: "Architect",
      percentile: 85,
      threatClassification: "City Block-Level Potential",
      domains: [{ name: "Mind", score: 35, tier: "T5" }],
      powers: [
        { name: "Telepathy", tier: "T5", flavorScore: 3, domain: "Mind", classification: "co-primary" },
        { name: "Mind Control", tier: "T5", flavorScore: 3, domain: "Mind", classification: "co-primary" }
      ],
      threatTags: ["Mind Control"]
    },
    createdAt: "2026-05-24T00:00:00.000Z",
    updatedAt: "2026-05-24T00:00:00.000Z"
  }, null, 2) + "\n");

  return file;
}

test("creates campaign from locked character files", () => {
  const rootDir = tempRoot();
  const characterFile = writeLockedCharacter(rootDir);

  const campaign = createCampaign({
    campaignId: "campaign-test",
    title: "The Emergence",
    seed: "test-seed",
    characterFiles: [characterFile],
    createdAt: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(campaign.party.length, 1);
  assert.equal(campaign.party[0].activeRole, "Architect");
  assert.equal(campaign.party[0].character.powers.length, 2);
  assert.equal(campaign.state.act, 1);
  assert.equal(campaign.state.scene, 1);
});

test("DM context packet preserves locked sheet truth", () => {
  const rootDir = tempRoot();
  const characterFile = writeLockedCharacter(rootDir);

  const campaign = createCampaign({
    campaignId: "campaign-test",
    title: "The Emergence",
    characterFiles: [characterFile]
  });

  const packet = buildDmContextPacket(campaign);

  assert.equal(packet.party[0].activeRole, "Architect");
  assert.deepEqual(packet.party[0].threatTags, ["Mind Control"]);
  assert.equal(packet.dmRules.some((rule) => rule.includes("Do not invent new powers")), true);
});

test("scene prompt is deterministic for same campaign state and intent", () => {
  const rootDir = tempRoot();
  const characterFile = writeLockedCharacter(rootDir);

  const campaign = createCampaign({
    campaignId: "campaign-test",
    title: "The Emergence",
    seed: "same-seed",
    characterFiles: [characterFile]
  });

  const a = createScenePrompt({ campaign, playerIntent: "investigate the anomaly" });
  const b = createScenePrompt({ campaign, playerIntent: "investigate the anomaly" });

  assert.deepEqual(a, b);
  assert.equal(a.prompt.includes("Do not add powers"), true);
});

test("campaign save and load round-trips", () => {
  const rootDir = tempRoot();
  const characterFile = writeLockedCharacter(rootDir);

  const campaign = createCampaign({
    campaignId: "campaign-test",
    title: "The Emergence",
    characterFiles: [characterFile]
  });

  const saved = saveCampaign({ campaign, rootDir });
  const loaded = loadCampaign({ campaignId: "campaign-test", rootDir });

  assert.equal(saved.ok, true);
  assert.equal(loaded.ok, true);
  assert.equal(loaded.campaign.title, "The Emergence");
});

test("advance scene requires summary and increments scene state", () => {
  const rootDir = tempRoot();
  const characterFile = writeLockedCharacter(rootDir);

  const campaign = createCampaign({
    campaignId: "campaign-test",
    title: "The Emergence",
    characterFiles: [characterFile]
  });

  const next = advanceCampaignScene({
    campaign,
    summary: "Architect sensed a coordinated mind behind the anomaly.",
    newLocation: "abandoned transit station",
    threadToAdd: "A hidden signal is directing newly manifested civilians.",
    updatedAt: "2026-05-24T01:00:00.000Z"
  });

  assert.equal(next.state.scene, 2);
  assert.equal(next.state.location, "abandoned transit station");
  assert.equal(next.state.openThreads.length, campaign.state.openThreads.length + 1);
});
