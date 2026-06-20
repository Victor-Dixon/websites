export function requireDiscordLiveEnv(env = process.env) {
  if (env.DISCORD_PLATFORM_LIVE !== "1") {
    throw new Error("Discord REST adapter requires DISCORD_PLATFORM_LIVE=1");
  }

  if (!env.DISCORD_BOT_TOKEN) {
    throw new Error("Discord REST adapter requires DISCORD_BOT_TOKEN");
  }

  if (!env.DISCORD_GUILD_ID) {
    throw new Error("Discord REST adapter requires DISCORD_GUILD_ID");
  }

  return {
    token: env.DISCORD_BOT_TOKEN,
    guildId: env.DISCORD_GUILD_ID
  };
}

export function discordApiBase(env = process.env) {
  return env.DISCORD_API_BASE || "https://discord.com/api/v10";
}

export function createDiscordRestAdapter({
  env = process.env,
  fetchImpl = globalThis.fetch
} = {}) {
  const live = requireDiscordLiveEnv(env);

  if (typeof fetchImpl !== "function") {
    throw new Error("Discord REST adapter requires fetch implementation");
  }

  async function request(method, route, body = undefined) {
    const url = `${discordApiBase(env)}${route}`;
    const response = await fetchImpl(url, {
      method,
      headers: {
        Authorization: `Bot ${live.token}`,
        "Content-Type": "application/json"
      },
      body: body === undefined ? undefined : JSON.stringify(body)
    });

    const text = await response.text();
    const parsed = text ? JSON.parse(text) : null;

    if (!response.ok) {
      throw new Error(`Discord API ${method} ${route} failed: ${response.status} ${text}`);
    }

    return parsed;
  }

  async function createChannel({ name, type, parentId, topic }) {
    const payload = {
      name,
      type
    };

    if (parentId) payload.parent_id = parentId;
    if (topic) payload.topic = topic;

    return request("POST", `/guilds/${live.guildId}/channels`, payload);
  }

  async function createThread({ channelId, name }) {
    if (!channelId) throw new Error("createThread requires channelId");

    return request("POST", `/channels/${channelId}/threads`, {
      name,
      type: 11,
      auto_archive_duration: 1440
    });
  }

  async function pinMessage({ channelId, messageId }) {
    if (!channelId) throw new Error("pinMessage requires channelId");
    if (!messageId) throw new Error("pinMessage requires messageId");

    return request("PUT", `/channels/${channelId}/pins/${messageId}`);
  }

  return {
    execute(action) {
      switch (action.type) {
        case "create_campaign_category":
          return createChannel({
            name: action.name,
            type: 4
          });

        case "create_text_channel":
          return createChannel({
            name: action.name,
            type: 0,
            parentId: action.parentId,
            topic: action.reason
          });

        case "create_private_gm_channel":
          return createChannel({
            name: action.name,
            type: 0,
            parentId: action.parentId,
            topic: action.reason
          });

        case "create_voice_channel":
          return createChannel({
            name: action.name,
            type: 2,
            parentId: action.parentId
          });

        case "create_thread":
          return createThread({
            channelId: action.parentChannelId,
            name: action.name
          });

        case "pin_message":
          return pinMessage({
            channelId: action.channelId,
            messageId: action.messageId
          });

        default:
          throw new Error(`Unsupported Discord live action: ${action.type}`);
      }
    }
  };
}

export function createMockDiscordRestAdapter() {
  const calls = [];

  return {
    calls,
    execute(action) {
      calls.push(action);
      return {
        ok: true,
        mocked: true,
        actionType: action.type,
        name: action.name || action.channel || null
      };
    }
  };
}
