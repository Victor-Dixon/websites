const TOKEN = process.env.DISCORD_BOT_TOKEN;
const TARGET_GUILD_ID = process.env.TARGET_GUILD_ID;
const API = process.env.DISCORD_API_BASE || "https://discord.com/api/v10";

async function discord(method, route) {
  const res = await fetch(`${API}${route}`, {
    method,
    headers: {
      Authorization: `Bot ${TOKEN}`,
      "Content-Type": "application/json"
    }
  });

  const text = await res.text();
  if (!res.ok) {
    throw new Error(`${method} ${route} failed ${res.status}: ${text}`);
  }

  return text ? JSON.parse(text) : null;
}

const guilds = await discord("GET", "/users/@me/guilds");
const found = guilds.find((guild) => guild.id === TARGET_GUILD_ID);

if (!found) {
  console.log(JSON.stringify({
    ok: false,
    targetGuildId: TARGET_GUILD_ID,
    reason: "Bot cannot see this guild. Invite bot to server or verify ID.",
    visibleGuilds: guilds.map((guild) => ({ id: guild.id, name: guild.name }))
  }, null, 2));
  process.exit(2);
}

console.log(JSON.stringify({
  ok: true,
  targetGuildId: TARGET_GUILD_ID,
  guild: {
    id: found.id,
    name: found.name,
    permissions: found.permissions
  }
}, null, 2));
