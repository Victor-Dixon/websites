import fs from "node:fs";
import path from "node:path";
import { createSeededRng } from "../../combat-engine/src/rng.mjs";

export function loadLockedCharacterFile(filePath) {
  if (!fs.existsSync(filePath)) {
    throw new Error(`Locked character file not found: ${filePath}`);
  }

  const data = JSON.parse(fs.readFileSync(filePath, "utf8"));

  if (!data.locked || !data.discordUserId || !data.character) {
    throw new Error(`Invalid locked character file: ${filePath}`);
  }

  return data;
}

export function createCampaign({
  campaignId,
  title,
  dmMode = "ai_dm",
  seed = "campaign-001",
  characterFiles = [],
  createdAt = new Date().toISOString()
}) {
  if (!campaignId) throw new Error("campaignId is required");
  if (!title) throw new Error("title is required");
  if (!Array.isArray(characterFiles) || characterFiles.length === 0) {
    throw new Error("At least one locked character file is required");
  }

  const party = characterFiles.map(loadLockedCharacterFile);

  return {
    schemaVersion: 1,
    campaignId,
    title,
    dmMode,
    seed,
    status: "active",
    createdAt,
    updatedAt: createdAt,
    party: party.map((entry) => ({
      discordUserId: entry.discordUserId,
      discordUsername: entry.discordUsername,
      activeRole: entry.activeRole,
      character: entry.character
    })),
    state: {
      act: 1,
      scene: 1,
      location: "AEGIS intake safehouse",
      tension: 1,
      openThreads: [
        "The party has been flagged by AEGIS after abnormal manifestation signatures.",
        "A city-wide anomaly pulse has started waking dormant threats."
      ],
      inventory: [],
      defeatedEnemies: [],
      unresolvedThreats: []
    }
  };
}

export function saveCampaign({
  campaign,
  rootDir = "data/campaigns"
}) {
  fs.mkdirSync(rootDir, { recursive: true });
  const file = path.join(rootDir, `${campaign.campaignId}.json`);
  fs.writeFileSync(file, JSON.stringify(campaign, null, 2) + "\n");
  return { ok: true, file, campaign };
}

export function loadCampaign({
  campaignId,
  rootDir = "data/campaigns"
}) {
  const file = path.join(rootDir, `${campaignId}.json`);
  if (!fs.existsSync(file)) {
    return { ok: false, status: "missing", file };
  }

  return {
    ok: true,
    status: "loaded",
    file,
    campaign: JSON.parse(fs.readFileSync(file, "utf8"))
  };
}

export function buildDmContextPacket(campaign) {
  return {
    campaignId: campaign.campaignId,
    title: campaign.title,
    dmRules: [
      "Locked character sheets are truth.",
      "Do not invent new powers, tiers, domains, or threat tags.",
      "Use Spark Protocol scale: city-wide is the ceiling.",
      "Narrate uncertainty, but do not override runtime facts.",
      "When combat starts, request or emit a battle packet instead of deciding by vibes.",
      "Advance one scene at a time."
    ],
    party: campaign.party.map((member) => ({
      discordUserId: member.discordUserId,
      activeRole: member.activeRole,
      percentile: member.character.percentile,
      threatClassification: member.character.threatClassification,
      domains: member.character.domains,
      powers: member.character.powers,
      threatTags: member.character.threatTags
    })),
    state: campaign.state
  };
}

export function createScenePrompt({
  campaign,
  playerIntent = "continue",
  seed = campaign.seed
}) {
  const rng = createSeededRng(`${seed}:${campaign.state.act}:${campaign.state.scene}:${playerIntent}`);
  const roll = Math.floor(rng() * 100) + 1;

  const context = buildDmContextPacket(campaign);

  return {
    schemaVersion: 1,
    type: "ai_dm_scene_prompt",
    campaignId: campaign.campaignId,
    sceneKey: `act-${campaign.state.act}-scene-${campaign.state.scene}`,
    seed,
    roll,
    playerIntent,
    context,
    prompt: [
      "You are the AI DM for a Spark Protocol campaign.",
      "Use the provided locked context as the only source of character truth.",
      "Open with a vivid scene at the current location.",
      "Give the player 3 concrete choices.",
      "Do not resolve combat without a battle packet.",
      "Do not add powers that are not listed on the locked sheets.",
      `Current player intent: ${playerIntent}`,
      `Scene roll: ${roll}`
    ].join("\n")
  };
}

export function advanceCampaignScene({
  campaign,
  summary,
  newLocation,
  tensionDelta = 1,
  threadToAdd,
  updatedAt = new Date().toISOString()
}) {
  if (!summary) throw new Error("summary is required to advance campaign scene");

  const next = structuredClone(campaign);
  next.state.scene += 1;
  next.state.tension = Math.max(0, next.state.tension + tensionDelta);
  next.state.lastSceneSummary = summary;
  next.state.location = newLocation || next.state.location;

  if (threadToAdd) {
    next.state.openThreads.push(threadToAdd);
  }

  next.updatedAt = updatedAt;
  return next;
}
