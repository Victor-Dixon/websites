import { completeQuizAndLockCharacter } from "../../packages/discord-flow/src/quizToLockedCharacter.mjs";

const result = completeQuizAndLockCharacter({
  rootDir: "data/characters",
  discordUserId: "100000000000002",
  discordUsername: "victor_demo",
  quizVersion: "spark-protocol-v5",
  quizResult: {
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
  },
  force: true,
  now: "2026-05-24T00:00:00.000Z"
});

console.log(JSON.stringify(result, null, 2));
