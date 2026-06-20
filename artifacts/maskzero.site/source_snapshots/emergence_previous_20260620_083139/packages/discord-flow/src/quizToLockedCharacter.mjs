import { buildCharacterFromQuiz } from "../../character-engine/src/scoring.mjs";
import {
  loadLockedCharacter,
  saveLockedCharacter
} from "../../character-engine/src/characterStore.mjs";

export function completeQuizAndLockCharacter({
  rootDir = "data/characters",
  discordUserId,
  discordUsername,
  quizVersion = "spark-protocol-v5",
  quizResult,
  force = false,
  now = new Date().toISOString()
}) {
  if (!quizResult || !quizResult.codename) {
    throw new Error("quizResult.codename is required");
  }

  const existing = loadLockedCharacter({ rootDir, discordUserId });
  if (existing.ok && !force) {
    return {
      ok: false,
      status: "character_already_locked",
      reason: "Discord user already has a locked character. Reroll requires explicit force.",
      file: existing.file,
      lockedCharacter: existing.character
    };
  }

  const sheet = buildCharacterFromQuiz({
    codename: quizResult.codename,
    domainScores: quizResult.domainScores,
    flavorScores: quizResult.flavorScores
  });

  const saved = saveLockedCharacter({
    rootDir,
    discordUserId,
    discordUsername,
    quizVersion,
    sheet,
    force,
    now
  });

  return {
    ok: saved.ok,
    status: saved.status,
    file: saved.file,
    lockedCharacter: saved.character
  };
}
