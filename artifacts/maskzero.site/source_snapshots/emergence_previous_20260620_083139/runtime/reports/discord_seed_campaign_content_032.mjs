import fs from "node:fs";

const TOKEN = process.env.DISCORD_BOT_TOKEN;
const GUILD_ID = process.env.DISCORD_GUILD_ID;
const API = process.env.DISCORD_API_BASE || "https://discord.com/api/v10";

const layout = JSON.parse(
  fs.readFileSync("runtime/reports/discord_live_layout_apply_017.json", "utf8")
);

if (layout.guildId !== GUILD_ID) {
  throw new Error(`Layout guild mismatch: layout=${layout.guildId} env=${GUILD_ID}`);
}

const locked = JSON.parse(fs.readFileSync("data/characters/100000000000002.json", "utf8"));
const campaign = JSON.parse(fs.readFileSync("data/campaigns/the-emergence-demo.json", "utf8"));

const report = {
  lane: "discord_seed_campaign_content_repair_032",
  mode: "live",
  guildId: GUILD_ID,
  posted: [],
  errors: []
};

async function discord(method, route, body) {
  const res = await fetch(`${API}${route}`, {
    method,
    headers: {
      Authorization: `Bot ${TOKEN}`,
      "Content-Type": "application/json"
    },
    body: body === undefined ? undefined : JSON.stringify(body)
  });

  const text = await res.text();
  const json = text ? JSON.parse(text) : null;

  if (!res.ok) {
    throw new Error(`${method} ${route} failed ${res.status}: ${text}`);
  }

  return json;
}

async function post(channelId, key, content) {
  const message = await discord("POST", `/channels/${channelId}/messages`, {
    content,
    allowed_mentions: { parse: [] }
  });

  report.posted.push({ key, channelId, messageId: message.id });
  return message;
}

async function postAndPin(channelId, key, content) {
  const message = await post(channelId, key, content);
  await discord("PUT", `/channels/${channelId}/pins/${message.id}`);
  const entry = report.posted.find((item) => item.messageId === message.id);
  if (entry) entry.pinned = true;
  return message;
}

const channels = layout.summary.channels;
const sheet = locked.character;

const characterSheetMd = [
  `# ${sheet.name}`,
  ``,
  `**Discord User:** ${locked.discordUsername}`,
  `**Locked Role:** ${locked.activeRole}`,
  `**Percentile:** ${sheet.percentile}`,
  `**Threat Classification:** ${sheet.threatClassification}`,
  ``,
  `## Domains`,
  ...sheet.domains.map((domain) => `- **${domain.name}** — ${domain.tier} (${domain.score})`),
  ``,
  `## Powers`,
  ...sheet.powers.map((power) => `- **${power.name}** — ${power.tier} / ${power.classification || "primary"} / ${power.domain}`),
  ``,
  `## Threat Tags`,
  ...(sheet.threatTags.length ? sheet.threatTags.map((tag) => `- ${tag}`) : ["- None"]),
  ``,
  `This sheet is locked. Powers, tiers, domains, percentile, and threat tags cannot be changed by narration.`
].join("\n");

const tableIntroMd = [
  `# The Emergence: First Signal`,
  ``,
  `The first wave was quiet.`,
  ``,
  `No explosion. No sky splitting open. Just a pressure behind the eyes, a city-wide flicker through nerves and screens at the same time.`,
  ``,
  `AEGIS calls it an anomaly pulse. People online are calling it a miracle. The emergency scanners disagree.`,
  ``,
  `Architect wakes with a clean, terrible certainty: somewhere in the city, someone else woke up too — and they are already reaching outward.`,
  ``,
  `## Choose Your First Move`,
  `1. Go to the AEGIS intake safehouse and learn what they know.`,
  `2. Track the source of the pulse through the city’s signal noise.`,
  `3. Enter the crowd where the first public manifestation was reported.`,
  ``,
  `Reply in this channel with your choice. The AI DM advances one scene at a time.`
].join("\n");

const battleLogMd = [
  `# Battle Log Rules`,
  ``,
  `This channel stores battle packets and adjudication reports.`,
  ``,
  `Combat must use locked character sheets. The AI DM may narrate uncertainty, terrain, stress, and bad luck, but may not invent powers or change tiers.`,
  ``,
  `Required battle packet shape:`,
  "```json",
  JSON.stringify({
    fighterA: "data/characters/100000000000002.json",
    fighterB: "data/characters/example_enemy.json",
    arena: {
      name: "abandoned transit station",
      hazards: ["wet rails", "flickering lights", "crowd panic"]
    },
    seed: "campaign-001-encounter-001"
  }, null, 2),
  "```"
].join("\n");

const recapMd = [
  `# Session Zero Recap`,
  ``,
  `Campaign layout created in **The Emergence**.`,
  `Architect locked as the first player character.`,
  `AI DM campaign state initialized at: **${campaign.state.location}**.`,
  ``,
  `Open threads:`,
  ...campaign.state.openThreads.map((thread) => `- ${thread}`),
  ``,
  `Next update should summarize the first player choice and advance to scene 2.`
].join("\n");

const gmNotesMd = [
  `# Private AI DM Operating Packet`,
  ``,
  `Rules:`,
  `- Locked character sheets are truth.`,
  `- Do not invent powers, tiers, domains, or threat tags.`,
  `- City-wide is the hard ceiling.`,
  `- Do not resolve combat by vibes. Emit/request a battle packet.`,
  `- Advance one scene at a time.`,
  `- No destructive Discord actions without operator approval.`,
  ``,
  `Current campaign state:`,
  "```json",
  JSON.stringify(campaign.state, null, 2),
  "```"
].join("\n");

await postAndPin(channels.characterSheets, "locked_architect_sheet", characterSheetMd);
await post(channels.table, "campaign_intro_first_signal", tableIntroMd);
await post(channels.battleLog, "battle_log_rules", battleLogMd);
await post(channels.recaps, "session_zero_recap", recapMd);
await post(channels.gmNotes, "private_ai_dm_operating_packet", gmNotesMd);

report.summary = {
  posted: report.posted.length,
  errors: report.errors.length,
  guildId: GUILD_ID,
  channels
};

fs.writeFileSync(
  "runtime/reports/discord_seed_campaign_content_032.json",
  JSON.stringify(report, null, 2) + "\n"
);

console.log(JSON.stringify(report, null, 2));
