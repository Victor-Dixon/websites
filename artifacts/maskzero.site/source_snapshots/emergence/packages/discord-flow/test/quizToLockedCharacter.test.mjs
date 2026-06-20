import assert from "node:assert/strict";
import fs from "node:fs";
import os from "node:os";
import path from "node:path";
import test from "node:test";

import { completeQuizAndLockCharacter } from "../src/quizToLockedCharacter.mjs";

function tempRoot() {
  return fs.mkdtempSync(path.join(os.tmpdir(), "spark-discord-flow-"));
}

const architectQuizResult = {
  codename: "Architect",
  domainScores: {
    Titan: 0,
    Velocity: 0,
    Energy: 0,
    Specter: 0,
    Omni: 0,
    Primal: 0,
    Mind: 35
  },
  flavorScores: {
    Mind: [3, 3, 0, 0, 0, 0]
  }
};

test("quiz completion builds and locks character to Discord user", () => {
  const rootDir = tempRoot();

  const result = completeQuizAndLockCharacter({
    rootDir,
    discordUserId: "100000000000001",
    discordUsername: "victor",
    quizVersion: "spark-protocol-v5",
    quizResult: architectQuizResult,
    now: "2026-05-24T00:00:00.000Z"
  });

  assert.equal(result.ok, true);
  assert.equal(result.status, "created");
  assert.equal(result.lockedCharacter.locked, true);
  assert.equal(result.lockedCharacter.discordUserId, "100000000000001");
  assert.equal(result.lockedCharacter.activeRole, "Architect");
  assert.equal(result.lockedCharacter.character.name, "Architect");
  assert.equal(result.lockedCharacter.character.domains[0].name, "Mind");
  assert.equal(result.lockedCharacter.character.powers.length, 2);
});

test("duplicate quiz completion refuses reroll without force", () => {
  const rootDir = tempRoot();

  completeQuizAndLockCharacter({
    rootDir,
    discordUserId: "100000000000001",
    discordUsername: "victor",
    quizResult: architectQuizResult,
    now: "2026-05-24T00:00:00.000Z"
  });

  const duplicate = completeQuizAndLockCharacter({
    rootDir,
    discordUserId: "100000000000001",
    discordUsername: "victor",
    quizResult: {
      ...architectQuizResult,
      codename: "Different"
    },
    now: "2026-05-24T01:00:00.000Z"
  });

  assert.equal(duplicate.ok, false);
  assert.equal(duplicate.status, "character_already_locked");
  assert.equal(duplicate.lockedCharacter.character.name, "Architect");
});

test("force reroll overwrites only when explicit", () => {
  const rootDir = tempRoot();

  completeQuizAndLockCharacter({
    rootDir,
    discordUserId: "100000000000001",
    discordUsername: "victor",
    quizResult: architectQuizResult,
    now: "2026-05-24T00:00:00.000Z"
  });

  const forced = completeQuizAndLockCharacter({
    rootDir,
    discordUserId: "100000000000001",
    discordUsername: "victor",
    quizResult: {
      ...architectQuizResult,
      codename: "Override"
    },
    force: true,
    now: "2026-05-24T01:00:00.000Z"
  });

  assert.equal(forced.ok, true);
  assert.equal(forced.status, "overwritten");
  assert.equal(forced.lockedCharacter.character.name, "Override");
});

test("invalid quiz math is rejected before lock", () => {
  const rootDir = tempRoot();

  assert.throws(
    () => completeQuizAndLockCharacter({
      rootDir,
      discordUserId: "100000000000001",
      discordUsername: "victor",
      quizResult: {
        codename: "Invalid",
        domainScores: architectQuizResult.domainScores,
        flavorScores: {
          Mind: [4, 4, 0, 0, 0, 0]
        }
      }
    }),
    /must total exactly 6/
  );
});
