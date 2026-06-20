import assert from "node:assert/strict";
import test from "node:test";

import {
  createDiscordRestAdapter,
  createMockDiscordRestAdapter,
  requireDiscordLiveEnv
} from "../src/discordRestAdapter.mjs";

test("discord live env requires explicit live gate", () => {
  assert.throws(
    () => requireDiscordLiveEnv({}),
    /DISCORD_PLATFORM_LIVE=1/
  );
});

test("discord live env requires token and guild id", () => {
  assert.throws(
    () => requireDiscordLiveEnv({
      DISCORD_PLATFORM_LIVE: "1"
    }),
    /DISCORD_BOT_TOKEN/
  );

  assert.throws(
    () => requireDiscordLiveEnv({
      DISCORD_PLATFORM_LIVE: "1",
      DISCORD_BOT_TOKEN: "token"
    }),
    /DISCORD_GUILD_ID/
  );
});

test("mock adapter records actions without network", () => {
  const adapter = createMockDiscordRestAdapter();

  const result = adapter.execute({
    type: "create_text_channel",
    name: "campaign-table"
  });

  assert.equal(result.ok, true);
  assert.equal(result.mocked, true);
  assert.equal(adapter.calls.length, 1);
});

test("real adapter maps category creation to Discord REST request", async () => {
  const requests = [];

  const adapter = createDiscordRestAdapter({
    env: {
      DISCORD_PLATFORM_LIVE: "1",
      DISCORD_BOT_TOKEN: "token",
      DISCORD_GUILD_ID: "guild",
      DISCORD_API_BASE: "https://discord.test/api/v10"
    },
    fetchImpl: async (url, options) => {
      requests.push({ url, options });
      return {
        ok: true,
        status: 200,
        async text() {
          return JSON.stringify({ id: "category-id", name: "Campaign" });
        }
      };
    }
  });

  const result = await adapter.execute({
    type: "create_campaign_category",
    name: "Campaign"
  });

  assert.equal(result.id, "category-id");
  assert.equal(requests.length, 1);
  assert.equal(requests[0].url, "https://discord.test/api/v10/guilds/guild/channels");
  assert.equal(requests[0].options.method, "POST");

  const body = JSON.parse(requests[0].options.body);
  assert.equal(body.name, "Campaign");
  assert.equal(body.type, 4);
});

test("real adapter rejects unsupported live action", () => {
  const adapter = createDiscordRestAdapter({
    env: {
      DISCORD_PLATFORM_LIVE: "1",
      DISCORD_BOT_TOKEN: "token",
      DISCORD_GUILD_ID: "guild"
    },
    fetchImpl: async () => {
      throw new Error("should not be called");
    }
  });

  assert.throws(
    () => adapter.execute({ type: "delete_channel", name: "nope" }),
    /Unsupported Discord live action/
  );
});
