const TOKEN = process.env.DISCORD_BOT_TOKEN;
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

const safe = guilds.map((guild) => ({
  id: guild.id,
  name: guild.name,
  owner: guild.owner || false,
  permissions: guild.permissions
}));

console.log(JSON.stringify({
  count: safe.length,
  guilds: safe
}, null, 2));
