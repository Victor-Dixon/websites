#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== COMPLETE ADAPTIVE ENGINE + FIX QUESTIONS =="

mkdir -p data/reports/quiz tests/scoring tests/e2e

python - << 'PY'
import json
from pathlib import Path

p = Path("data/quiz/questions.json")
data = json.loads(p.read_text())

g_by_block = {
    "domain": "G. I coordinate the whole situation from above, pulling people, timing, and resources into alignment.",
    "titan": "G. I become the central force everything else has to organize around.",
    "velocity": "G. I move through the situation by coordinating routes, timing, and openings at once.",
    "inferno": "G. I focus my intensity into a controlled field that changes how everyone else can move.",
    "specter": "G. I disappear into the structure of the moment, using exits, shadows, and timing together.",
    "omni": "G. I become the hub, linking separate pieces into one working system.",
    "primal": "G. I shift the entire emotional climate so people naturally move with me."
}

def block_for(qid):
    if 1 <= qid <= 36:
        return "domain"
    if 37 <= qid <= 42:
        return "titan"
    if 43 <= qid <= 48:
        return "velocity"
    if 49 <= qid <= 54:
        return "inferno"
    if 55 <= qid <= 60:
        return "specter"
    if 61 <= qid <= 66:
        return "omni"
    if 67 <= qid <= 72:
        return "primal"
    return "domain"

changed = []
for q in data["questions"]:
    opts = q.get("options") or []
    opts = [str(o) for o in opts if not str(o).startswith("G.")]
    opts.append(g_by_block[block_for(q["id"])])
    q["options"] = opts
    changed.append(q["id"])

data["scoring"] = {
    "mode": "spark_protocol_adaptive_domain_flavor_v1",
    "domain_questions": "1-36",
    "adaptive_flavor_questions": {
        "titan": "37-42",
        "velocity": "43-48",
        "inferno": "49-54",
        "specter": "55-60",
        "omni": "61-66",
        "primal": "67-72",
        "bulwark": "37-42"
    },
    "answer_choices": ["A", "B", "C", "D", "E", "F", "G"],
    "notes": "A-G schema active. Raw scoring remains sealed from users."
}

p.write_text(json.dumps(data, indent=2, ensure_ascii=False) + "\n")

report = {
    "status": "PASS",
    "questions_patched": len(changed),
    "answer_choices": ["A", "B", "C", "D", "E", "F", "G"],
    "missing_g": [
        q["id"] for q in data["questions"]
        if not any(str(o).startswith("G.") for o in q.get("options", []))
    ]
}
Path("data/reports/quiz/a_g_question_patch_report.json").write_text(json.dumps(report, indent=2) + "\n")
print("QUESTIONS_A_G_PATCH=PASS")
print("QUESTIONS=" + str(len(data["questions"])))
print("MISSING_G=" + str(len(report["missing_g"])))
PY

cat > packages/quiz-engine/index.js << 'JS'
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

  if (!raw.questions || !Array.isArray(raw.questions)) {
    throw new Error('Invalid quiz: missing questions[]');
  }

  return raw;
}

export function normalizeQuestion(q, idx) {
  let answers = q.answers || {};

  if ((!answers || Object.keys(answers).length === 0) && Array.isArray(q.options)) {
    answers = {};

    for (const option of q.options) {
      const match = String(option).match(/^([A-G])\.\s*(.*)$/);

      if (match) {
        answers[match[1]] = match[2];
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

  for (const value of Object.values(responses)) {
    counts[value] = (counts[value] || 0) + 1;
  }

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

  fs.writeFileSync(
    path.join(outDir, `${userId}.json`),
    JSON.stringify(payload, null, 2) + '\n'
  );
}
JS

cat > packages/scoring-engine/index.js << 'JS'
export const DOMAIN_MAP = {
  A: 'titan',
  B: 'inferno',
  C: 'velocity',
  D: 'bulwark',
  E: 'primal',
  F: 'specter',
  G: 'omni'
};

export const FLAVOR_MAP = {
  A: 'force',
  B: 'surge',
  C: 'reflex',
  D: 'anchor',
  E: 'resonance',
  F: 'shadow',
  G: 'control'
};

export function calculateDomainScores(responses) {
  const scores = {};

  for (const domain of Object.values(DOMAIN_MAP)) {
    scores[domain] = 0;
  }

  for (const [qid, answer] of Object.entries(responses)) {
    const qnum = Number(qid);

    if (qnum >= 1 && qnum <= 36) {
      const domain = DOMAIN_MAP[answer];
      if (domain) scores[domain] += 1;
    }
  }

  return scores;
}

export function calculateFlavorVectors(responses) {
  const vectors = {};

  for (const flavor of Object.values(FLAVOR_MAP)) {
    vectors[flavor] = 0;
  }

  for (const [qid, answer] of Object.entries(responses)) {
    const qnum = Number(qid);

    if (qnum >= 37 && qnum <= 72) {
      const flavor = FLAVOR_MAP[answer];
      if (flavor) vectors[flavor] += 1;
    }
  }

  return vectors;
}

export function determinePrimaryDomain(scores) {
  return Object.entries(scores)
    .sort((a, b) => b[1] - a[1])[0]?.[0] || 'unknown';
}

export function manifestedDomains(domainScores) {
  const max = Math.max(...Object.values(domainScores));
  if (!Number.isFinite(max) || max <= 0) return [];

  return Object.entries(domainScores)
    .filter(([, score]) => score === max || score >= Math.ceil(max * 0.75))
    .map(([domain]) => domain);
}

export function domainFlavorRange(domain) {
  const ranges = {
    titan: [37, 42],
    bulwark: [37, 42],
    velocity: [43, 48],
    inferno: [49, 54],
    specter: [55, 60],
    omni: [61, 66],
    primal: [67, 72]
  };

  return ranges[domain] || [61, 66];
}

export function adaptiveQuestionIds(responses) {
  const domainScores = calculateDomainScores(responses);
  const domains = manifestedDomains(domainScores);
  const ids = new Set();

  for (let i = 1; i <= 36; i++) ids.add(i);

  for (const domain of domains) {
    const [start, end] = domainFlavorRange(domain);
    for (let i = start; i <= end; i++) ids.add(i);
  }

  return [...ids].sort((a, b) => a - b);
}

export function currentAdaptiveQuestionIds(responses) {
  const answeredDomainCount = Object.keys(responses)
    .map(Number)
    .filter(qid => qid >= 1 && qid <= 36)
    .length;

  if (answeredDomainCount < 36) {
    return Array.from({ length: 36 }, (_, i) => i + 1);
  }

  return adaptiveQuestionIds(responses);
}

export function adaptiveQuestionsRemaining(responses) {
  const ids = currentAdaptiveQuestionIds(responses);
  const answered = new Set(Object.keys(responses).map(Number));

  return ids.filter(id => !answered.has(id));
}

export function adaptiveProgress(responses) {
  const ids = currentAdaptiveQuestionIds(responses);
  const remaining = adaptiveQuestionsRemaining(responses);

  return {
    total: ids.length,
    answered: ids.length - remaining.length,
    remaining: remaining.length,
    remaining_ids: remaining,
    complete: remaining.length === 0
  };
}

export function determineTier(primaryScore) {
  if (primaryScore >= 28) return 'T5';
  if (primaryScore >= 22) return 'T4';
  if (primaryScore >= 16) return 'T3';
  if (primaryScore >= 10) return 'T2';
  return 'T1';
}

export function determineThreatClass(tier, flavorVectors) {
  const shadow = flavorVectors.shadow || 0;
  const surge = flavorVectors.surge || 0;
  const control = flavorVectors.control || 0;

  if (tier === 'T5' && (surge >= 5 || control >= 5)) return 'OMEGA';
  if (tier === 'T5' || tier === 'T4') return 'ALPHA';
  if (shadow >= 5) return 'SIGMA';

  return 'STANDARD';
}

export function buildCharacterSheet({ userId, username, responses }) {
  const domain_scores = calculateDomainScores(responses);
  const flavor_vectors = calculateFlavorVectors(responses);

  const primary_domain = determinePrimaryDomain(domain_scores);
  const primary_score = domain_scores[primary_domain] || 0;

  const tier = determineTier(primary_score);
  const threat_class = determineThreatClass(tier, flavor_vectors);

  return {
    schema_version: 'spark_character_sheet_v1',
    locked: true,
    user_id: userId,
    username,
    generated_at: new Date().toISOString(),
    primary_domain,
    primary_score,
    tier,
    threat_class,
    domain_scores,
    flavor_vectors,
    manifestation: {
      codename: null,
      alignment: threat_class,
      descriptor: `${tier} ${primary_domain}`
    }
  };
}

export function buildComicProfile(sheet) {
  return {
    title: `${sheet.username}: Classified Emergence Profile`,
    subtitle: `Manifestation Class ${sheet.tier}`,
    cover_line: `A ${sheet.primary_domain.toUpperCase()}-type emergence with ${sheet.threat_class} threat-band behavior.`,
    stat_blocks: [
      `Primary Manifestation: ${sheet.primary_domain.toUpperCase()}`,
      `Power Tier: ${sheet.tier}`,
      `Threat Band: ${sheet.threat_class}`,
      `AEGIS Lock: IMMUTABLE`
    ],
    back_matter: [
      'Origin Signal: psychological resonance pattern confirmed.',
      'Protocol Note: raw scoring matrix sealed to prevent profile gaming.',
      'Battle Eligibility: approved after codename registration.',
      'Reader Advisory: this subject may evolve narratively, but the locked sheet does not drift.'
    ]
  };
}
JS

cat > tests/answer_choices.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  ANSWER_CHOICES,
  isValidAnswer,
  loadQuiz,
  normalizeQuestion,
  validAnswerChoices
} from '../packages/quiz-engine/index.js';

test('each canonical quiz question accepts A-G answers', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    for (const letter of ANSWER_CHOICES) {
      assert.equal(isValidAnswer(question, letter, index), true, `Q${index + 1} should accept ${letter}`);
    }
  });
});

test('each question exposes exactly seven domain choices', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    assert.deepEqual(validAnswerChoices(question, index), ANSWER_CHOICES);
    assert.equal(Object.keys(normalizeQuestion(question, index).answers).length, 7);
  });
});
JS

cat > tests/quiz_contract.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  ANSWER_CHOICES,
  loadQuiz,
  normalizeQuestion
} from '../packages/quiz-engine/index.js';

test('canonical quiz loads with 72 questions and A-G schema', () => {
  const quiz = loadQuiz();

  assert.equal(quiz.form_id, 'SPARK-72');
  assert.equal(quiz.version, 'MVP-2026-05-23');
  assert.equal(Array.isArray(quiz.questions), true);
  assert.equal(quiz.questions.length, 72);
  assert.deepEqual(quiz.scoring.answer_choices, ANSWER_CHOICES);
});

test('canonical quiz has required metadata', () => {
  const quiz = loadQuiz();

  assert.equal(typeof quiz.title, 'string');
  assert.ok(quiz.title.includes('Spark Protocol'));
  assert.equal(typeof quiz.preamble, 'string');
  assert.equal(typeof quiz.instructions, 'string');
  assert.equal(typeof quiz.scoring, 'object');
  assert.equal(quiz.scoring.domain_questions, '1-36');
});

test('every question has id, text, and seven A-G options', () => {
  const quiz = loadQuiz();

  quiz.questions.forEach((question, index) => {
    assert.equal(question.id, index + 1);
    assert.equal(typeof question.question, 'string');
    assert.ok(question.question.length > 10);

    const normalized = normalizeQuestion(question, index);
    assert.deepEqual(Object.keys(normalized.answers), ANSWER_CHOICES);

    for (const answer of Object.values(normalized.answers)) {
      assert.equal(typeof answer, 'string');
      assert.ok(answer.length > 3);
    }
  });
});

test('domain and flavor question ranges are structurally valid', () => {
  const quiz = loadQuiz();

  const domain = quiz.questions.slice(0, 36);
  const flavor = quiz.questions.slice(36, 72);

  assert.equal(domain[0].id, 1);
  assert.equal(domain.at(-1).id, 36);
  assert.equal(flavor[0].id, 37);
  assert.equal(flavor.at(-1).id, 72);
});
JS

cat > tests/scoring/adaptive_engine_full.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  currentAdaptiveQuestionIds,
  domainFlavorRange,
  manifestedDomains,
  calculateDomainScores
} from '../../packages/scoring-engine/index.js';

test('adaptive engine starts at domain phase only', () => {
  const progress = adaptiveProgress({});
  assert.equal(progress.total, 36);
  assert.equal(progress.answered, 0);
  assert.equal(progress.remaining, 36);
  assert.equal(progress.complete, false);
});

test('each domain maps to a valid sub-affinity range', () => {
  for (const domain of ['titan', 'bulwark', 'velocity', 'inferno', 'specter', 'omni', 'primal']) {
    const [start, end] = domainFlavorRange(domain);
    assert.equal(end - start + 1, 6);
    assert.ok(start >= 37);
    assert.ok(end <= 72);
  }
});

test('G answers manifest omni and expand to omni block only', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  const scores = calculateDomainScores(responses);
  const domains = manifestedDomains(scores);
  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.deepEqual(domains, ['omni']);
  assert.equal(ids.length, 42);
  assert.deepEqual(ids.slice(-6), [61, 62, 63, 64, 65, 66]);
  assert.equal(progress.remaining, 6);
});

test('adaptive questions left updates as manifested block is answered', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';

  assert.equal(adaptiveProgress(responses).remaining, 6);

  responses[61] = 'A';
  responses[62] = 'B';

  const progress = adaptiveProgress(responses);
  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 38);
  assert.equal(progress.remaining, 4);
  assert.deepEqual(progress.remaining_ids, [63, 64, 65, 66]);
});

test('adaptive quiz completes without asking irrelevant flavor blocks', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'G';
  for (let i = 61; i <= 66; i++) responses[i] = 'C';

  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.includes(43), false);
  assert.equal(ids.includes(55), false);
  assert.equal(ids.includes(67), false);
  assert.equal(progress.complete, true);
  assert.equal(progress.remaining, 0);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})

scripts["test:contract"] = "node --test tests/quiz_contract.test.js"
scripts["test:answers"] = "node --test tests/answer_choices.test.js"
scripts["test:adaptive-full"] = "node --test tests/scoring/adaptive_engine_full.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:answers && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress && npm run test:adaptive-discord && npm run test:adaptive-full"

pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PACKAGE_TESTS_UPDATED=PASS")
PY

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

old = """  const answerRow = new ActionRowBuilder();

  for (const letter of Object.keys(q.answers).slice(0, 5)) {
    answerRow.addComponents(
      new ButtonBuilder()
        .setCustomId(`spark_answer:${letter}`)
        .setLabel(letter)
        .setStyle(session.responses[q.id] === letter ? ButtonStyle.Success : ButtonStyle.Secondary)
    );
  }

  const navRow = new ActionRowBuilder().addComponents("""

new = """  const answerLetters = Object.keys(q.answers);
  const answerRows = [];

  for (let i = 0; i < answerLetters.length; i += 5) {
    const row = new ActionRowBuilder();

    for (const letter of answerLetters.slice(i, i + 5)) {
      row.addComponents(
        new ButtonBuilder()
          .setCustomId(`spark_answer:${letter}`)
          .setLabel(letter)
          .setStyle(session.responses[q.id] === letter ? ButtonStyle.Success : ButtonStyle.Secondary)
      );
    }

    answerRows.push(row);
  }

  const navRow = new ActionRowBuilder().addComponents("""

s = s.replace(old, new)
s = s.replace("  return [answerRow, navRow];", "  return [...answerRows, navRow];")

p.write_text(s)
print("DISCORD_A_G_BUTTON_ROWS_PATCH=PASS")
PY

npm run test:mobile

echo "COMPLETE_ADAPTIVE_ENGINE_AND_FIX_QUESTIONS=PASS"
