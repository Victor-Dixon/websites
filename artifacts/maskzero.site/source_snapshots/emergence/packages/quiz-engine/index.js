import fs from 'node:fs';
import path from 'node:path';

export const ANSWER_CHOICES = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];

export function repoRoot() {
  return path.resolve(new URL('../..', import.meta.url).pathname);
}

export function resolveRepoPath(relativePath) {
  return path.join(repoRoot(), relativePath);
}

export function loadQuiz(relativePath = 'data/quiz/questions.json') {
  const raw = JSON.parse(fs.readFileSync(resolveRepoPath(relativePath), 'utf8'));
  if (!raw.questions || !Array.isArray(raw.questions)) throw new Error('Invalid quiz: missing questions[]');
  return raw;
}

export function normalizeQuestion(q, idx) {
  const answers = {};

  if (q.answers && Object.keys(q.answers).length > 0) {
    for (const letter of ANSWER_CHOICES) {
      if (q.answers[letter]) answers[letter] = q.answers[letter];
    }
  }

  if (Array.isArray(q.options)) {
    for (const option of q.options) {
      const text = String(option).trim();
      const letter = text[0];
      if (ANSWER_CHOICES.includes(letter) && text[1] === '.') {
        answers[letter] = text.slice(2).trim();
      }
    }
  }

  return {
    id: q.id ?? idx + 1,
    question: q.question || q.text || '',
    answers
  };
}

export function validAnswerChoices(question, index = 0) {
  return Object.keys(normalizeQuestion(question, index).answers);
}

export function isValidAnswer(question, answer, index = 0) {
  return validAnswerChoices(question, index).includes(answer);
}

export function buildBasicResults(quiz, responses) {
  const counts = {};
  for (const value of Object.values(responses)) counts[value] = (counts[value] || 0) + 1;

  const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
  const primary = sorted[0]?.[0] || 'UNKNOWN';

  return {
    total_answered: Object.keys(responses).length,
    total_questions: quiz.questions.length,
    counts,
    primary,
    locked: Object.keys(responses).length === quiz.questions.length
  };
}

export function saveQuizResult(userId, payload) {
  const outDir = resolveRepoPath('data/state/quiz_results');
  fs.mkdirSync(outDir, { recursive: true });
  fs.writeFileSync(path.join(outDir, `${userId}.json`), JSON.stringify(payload, null, 2) + '\n');
}
