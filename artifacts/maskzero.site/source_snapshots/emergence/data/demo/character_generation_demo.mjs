import fs from "node:fs";
import { buildCharacterFromQuiz } from "../../packages/character-engine/src/scoring.mjs";

const quiz = JSON.parse(
  fs.readFileSync("fixtures/quiz/livewire.quiz.json", "utf8")
);

const character = buildCharacterFromQuiz(quiz);

console.log(JSON.stringify(character, null, 2));
