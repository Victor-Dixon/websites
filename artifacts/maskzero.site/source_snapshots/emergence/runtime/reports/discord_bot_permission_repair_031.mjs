const clientId = process.env.CLIENT_ID || process.env.DISCORD_CLIENT_ID;
const guildId = process.env.GUILD_ID || process.env.DISCORD_GUILD_ID;

// Required for campaign platform:
// View Channels, Manage Channels, Send Messages, Create Public Threads,
// Create Private Threads, Send Messages in Threads, Manage Messages for pins.
const PERMS = {
  VIEW_CHANNEL: 1024n,
  MANAGE_CHANNELS: 16n,
  SEND_MESSAGES: 2048n,
  MANAGE_MESSAGES: 8192n,
  CREATE_PUBLIC_THREADS: 34359738368n,
  CREATE_PRIVATE_THREADS: 68719476736n,
  SEND_MESSAGES_IN_THREADS: 274877906944n,
  READ_MESSAGE_HISTORY: 65536n
};

let required = 0n;
for (const bit of Object.values(PERMS)) required |= bit;

const inviteUrl =
  `https://discord.com/oauth2/authorize?client_id=${clientId}` +
  `&permissions=${required.toString()}` +
  `&integration_type=0&scope=bot+applications.commands` +
  `&guild_id=${guildId}` +
  `&disable_guild_select=true`;

const report = {
  lane: "discord_bot_permission_repair_031",
  targetGuildId: guildId,
  requiredPermissionsDecimal: required.toString(),
  requiredPermissions: Object.keys(PERMS),
  inviteUrl,
  instructions: [
    "Open inviteUrl as a server admin.",
    "Authorize the bot into The Emergence.",
    "Confirm it has Manage Channels, Send Messages, Create Threads, Read Message History, and Manage Messages.",
    "Then rerun apply_emergence_with_discord_bot_env_030."
  ]
};

console.log(JSON.stringify(report, null, 2));
