import fs from "node:fs";
import path from "node:path";

export function safeDiscordUserId(discordUserId) {
  const value = String(discordUserId || "").trim();
  if (!/^[0-9]{5,32}$/.test(value)) {
    throw new Error(`Invalid discordUserId: ${discordUserId}`);
  }
  return value;
}

export function characterPath(rootDir, discordUserId) {
  return path.join(rootDir, `${safeDiscordUserId(discordUserId)}.json`);
}

export function buildLockedCharacter({
  discordUserId,
  discordUsername,
  quizVersion,
  sheet,
  createdAt = new Date().toISOString()
}) {
  if (!sheet || !sheet.name || !Array.isArray(sheet.powers)) {
    throw new Error("Invalid sheet: expected name and powers[]");
  }

  return {
    schemaVersion: 1,
    locked: true,
    discordUserId: safeDiscordUserId(discordUserId),
    discordUsername: discordUsername || null,
    quizVersion: quizVersion || "unknown",
    character: sheet,
    activeRole: sheet.name,
    createdAt,
    updatedAt: createdAt
  };
}

export function saveLockedCharacter({
  rootDir = "data/characters",
  discordUserId,
  discordUsername,
  quizVersion,
  sheet,
  force = false,
  now = new Date().toISOString()
}) {
  fs.mkdirSync(rootDir, { recursive: true });
  const file = characterPath(rootDir, discordUserId);

  if (fs.existsSync(file) && !force) {
    const existing = JSON.parse(fs.readFileSync(file, "utf8"));
    return {
      ok: false,
      status: "already_locked",
      file,
      character: existing
    };
  }

  const locked = buildLockedCharacter({
    discordUserId,
    discordUsername,
    quizVersion,
    sheet,
    createdAt: now
  });

  fs.writeFileSync(file, JSON.stringify(locked, null, 2) + "\n");

  return {
    ok: true,
    status: force ? "overwritten" : "created",
    file,
    character: locked
  };
}

export function loadLockedCharacter({
  rootDir = "data/characters",
  discordUserId
}) {
  const file = characterPath(rootDir, discordUserId);
  if (!fs.existsSync(file)) {
    return { ok: false, status: "missing", file };
  }

  return {
    ok: true,
    status: "loaded",
    file,
    character: JSON.parse(fs.readFileSync(file, "utf8"))
  };
}
