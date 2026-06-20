import assert from "node:assert/strict";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";
import test from "node:test";
import {
  buildLockedCharacter,
  loadLockedCharacter,
  saveLockedCharacter
} from "../src/characterStore.mjs";

const sheet = {
  name: "Livewire",
  percentile: 89,
  threatClassification: "City-Wide Potential",
  domains: [{ name: "Energy", tier: "T5", score: 35 }],
  powers: [{ name: "Electrokinesis", tier: "T5" }],
  threatTags: []
};

function tempRoot() {
  return fs.mkdtempSync(path.join(os.tmpdir(), "spark-character-store-"));
}

test("builds a locked character tied to Discord user ID", () => {
  const locked = buildLockedCharacter({
    discordUserId: "123456789",
    discordUsername: "victor",
    quizVersion: "v5",
    sheet,
    createdAt: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(locked.locked, true);
  assert.equal(locked.discordUserId, "123456789");
  assert.equal(locked.activeRole, "Livewire");
  assert.equal(locked.character.powers[0].name, "Electrokinesis");
});

test("rejects invalid Discord IDs", () => {
  assert.throws(() => buildLockedCharacter({
    discordUserId: "not-a-user",
    sheet
  }));
});

test("saves and loads persistent locked character", () => {
  const rootDir = tempRoot();

  const saved = saveLockedCharacter({
    rootDir,
    discordUserId: "123456789",
    discordUsername: "victor",
    quizVersion: "v5",
    sheet,
    now: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(saved.ok, true);
  assert.equal(saved.status, "created");

  const loaded = loadLockedCharacter({ rootDir, discordUserId: "123456789" });
  assert.equal(loaded.ok, true);
  assert.equal(loaded.character.character.name, "Livewire");
});

test("does not overwrite existing locked character without force", () => {
  const rootDir = tempRoot();

  saveLockedCharacter({
    rootDir,
    discordUserId: "123456789",
    sheet,
    now: "2026-05-24T00:00:00.000Z"
  });

  const blocked = saveLockedCharacter({
    rootDir,
    discordUserId: "123456789",
    sheet: { ...sheet, name: "Backstop" },
    now: "2026-05-24T01:00:00.000Z"
  });

  assert.equal(blocked.ok, false);
  assert.equal(blocked.status, "already_locked");
  assert.equal(blocked.character.character.name, "Livewire");
});

test("force overwrite is explicit", () => {
  const rootDir = tempRoot();

  saveLockedCharacter({
    rootDir,
    discordUserId: "123456789",
    sheet,
    now: "2026-05-24T00:00:00.000Z"
  });

  const overwritten = saveLockedCharacter({
    rootDir,
    discordUserId: "123456789",
    sheet: { ...sheet, name: "Backstop" },
    force: true,
    now: "2026-05-24T01:00:00.000Z"
  });

  assert.equal(overwritten.ok, true);
  assert.equal(overwritten.status, "overwritten");
  assert.equal(overwritten.character.character.name, "Backstop");
});
