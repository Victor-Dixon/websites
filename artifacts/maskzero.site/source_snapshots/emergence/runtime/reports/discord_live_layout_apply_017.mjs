import fs from "node:fs";

const TOKEN = process.env.DISCORD_BOT_TOKEN;
const GUILD_ID = process.env.DISCORD_GUILD_ID;
const API = process.env.DISCORD_API_BASE || "https://discord.com/api/v10";

const report = {
  lane: "discord_live_layout_apply_017",
  mode: "live",
  guildId: GUILD_ID,
  created: [],
  reused: [],
  skipped: [],
  errors: []
};

function slugify(value) {
  return String(value)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "")
    .slice(0, 80);
}

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

async function getChannels() {
  return await discord("GET", `/guilds/${GUILD_ID}/channels`);
}

function findChannel(channels, { name, type, parentId = null }) {
  return channels.find((channel) => {
    if (channel.name !== name) return false;
    if (channel.type !== type) return false;
    if (parentId && channel.parent_id !== parentId) return false;
    return true;
  });
}

async function ensureChannel({ channels, name, type, parentId = null, topic = null }) {
  const existing = findChannel(channels, { name, type, parentId });

  if (existing) {
    report.reused.push({ type, name, id: existing.id, parentId });
    return existing;
  }

  const payload = { name, type };
  if (parentId) payload.parent_id = parentId;
  if (topic && type === 0) payload.topic = topic;

  const created = await discord("POST", `/guilds/${GUILD_ID}/channels`, payload);
  report.created.push({ type, name, id: created.id, parentId });
  channels.push(created);
  return created;
}

async function ensureThread({ parentChannelId, name }) {
  // Existing active threads are not included in guild channel list reliably.
  // Try create once; if Discord says duplicate or unsupported, record and continue.
  try {
    const created = await discord("POST", `/channels/${parentChannelId}/threads`, {
      name,
      type: 11,
      auto_archive_duration: 1440
    });

    report.created.push({ type: "thread", name, id: created.id, parentChannelId });
    return created;
  } catch (err) {
    report.skipped.push({
      type: "thread",
      name,
      parentChannelId,
      reason: String(err.message || err)
    });
    return null;
  }
}

const campaign = JSON.parse(fs.readFileSync("data/campaigns/the-emergence-demo.json", "utf8"));

const categoryName = `🎲 ${campaign.title}`;
const base = slugify(campaign.title);

let channels = await getChannels();

const category = await ensureChannel({
  channels,
  name: categoryName,
  type: 4
});

const table = await ensureChannel({
  channels,
  name: `${base}-table`,
  type: 0,
  parentId: category.id,
  topic: "Main table chat"
});

const sheets = await ensureChannel({
  channels,
  name: `${base}-character-sheets`,
  type: 0,
  parentId: category.id,
  topic: "Locked character sheet display"
});

const battleLog = await ensureChannel({
  channels,
  name: `${base}-battle-log`,
  type: 0,
  parentId: category.id,
  topic: "Battle packets and adjudication logs"
});

const recaps = await ensureChannel({
  channels,
  name: `${base}-recaps`,
  type: 0,
  parentId: category.id,
  topic: "Session recap feed"
});

const gmNotes = await ensureChannel({
  channels,
  name: `${base}-gm-notes`,
  type: 0,
  parentId: category.id,
  topic: "Private AI DM planning notes"
});

const voice = await ensureChannel({
  channels,
  name: `${base}-voice`,
  type: 2,
  parentId: category.id
});

await ensureThread({
  parentChannelId: sheets.id,
  name: "architect-sheet-thread"
});

report.summary = {
  created: report.created.length,
  reused: report.reused.length,
  skipped: report.skipped.length,
  errors: report.errors.length,
  categoryId: category.id,
  channels: {
    table: table.id,
    characterSheets: sheets.id,
    battleLog: battleLog.id,
    recaps: recaps.id,
    gmNotes: gmNotes.id,
    voice: voice.id
  }
};

fs.writeFileSync(
  "runtime/reports/discord_live_layout_apply_017.json",
  JSON.stringify(report, null, 2) + "\n"
);

console.log(JSON.stringify(report, null, 2));
