#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== WIRE ADAPTIVE QUIZ TO DISCORD =="

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

s = s.replace(
"""import {
  buildCharacterSheet
} from '../../../packages/scoring-engine/index.js';""",
"""import {
  adaptiveProgress,
  buildCharacterSheet,
  buildComicProfile,
  currentAdaptiveQuestionIds
} from '../../../packages/scoring-engine/index.js';"""
)

s = s.replace(
"""function buildQuizEmbed(session) {
  const q = normalizeQuestion(quiz.questions[session.index], session.index);
  const answered = Object.keys(session.responses).length;
  const current = session.responses[q.id];""",
"""function activeQuestionId(session) {
  const ids = currentAdaptiveQuestionIds(session.responses);
  const safeIndex = Math.min(session.index, ids.length - 1);
  return ids[safeIndex];
}

function activeQuestion(session) {
  const qid = activeQuestionId(session);
  return normalizeQuestion(quiz.questions[qid - 1], qid - 1);
}

function syncSessionIndex(session) {
  const ids = currentAdaptiveQuestionIds(session.responses);
  if (session.index >= ids.length) session.index = ids.length - 1;
  if (session.index < 0) session.index = 0;
}

function buildQuizEmbed(session) {
  syncSessionIndex(session);
  const ids = currentAdaptiveQuestionIds(session.responses);
  const q = activeQuestion(session);
  const progress = adaptiveProgress(session.responses);
  const current = session.responses[q.id];"""
)

s = s.replace(
"""    .setTitle(`The Emergence Classification — Q${session.index + 1}/${quiz.questions.length}`)""",
"""    .setTitle(`The Emergence Classification — Q${session.index + 1}/${ids.length}`)"""
)

s = s.replace(
"""      text: `Progress: ${progressBar(answered, quiz.questions.length)} (${answered}/${quiz.questions.length})`
    });""",
"""      text: `Progress: ${progressBar(progress.answered, progress.total)} (${progress.answered}/${progress.total}) | Questions left: ${progress.remaining}`
    });"""
)

s = s.replace(
"""function buildQuizRows(session) {
  const q = normalizeQuestion(quiz.questions[session.index], session.index);""",
"""function buildQuizRows(session) {
  syncSessionIndex(session);
  const ids = currentAdaptiveQuestionIds(session.responses);
  const progress = adaptiveProgress(session.responses);
  const q = activeQuestion(session);"""
)

s = s.replace(
""".setDisabled(session.index >= quiz.questions.length - 1),""",
""".setDisabled(session.index >= ids.length - 1),"""
)

s = s.replace(
""".setDisabled(Object.keys(session.responses).length >= quiz.questions.length),""",
""".setDisabled(progress.complete),"""
)

s = s.replace(
""".setDisabled(Object.keys(session.responses).length < quiz.questions.length)""",
""".setDisabled(!progress.complete)"""
)

s = s.replace(
"""      if (session.index < quiz.questions.length - 1) {
        session.index += 1;
      }""",
"""      const ids = currentAdaptiveQuestionIds(session.responses);
      if (session.index < ids.length - 1) {
        session.index += 1;
      }"""
)

s = s.replace(
"""      session.index = Math.min(quiz.questions.length - 1, session.index + 1);""",
"""      const ids = currentAdaptiveQuestionIds(session.responses);
      session.index = Math.min(ids.length - 1, session.index + 1);"""
)

s = s.replace(
"""      const idx = quiz.questions.findIndex((item, i) => {
        const nq = normalizeQuestion(item, i);
        return !session.responses[nq.id];
      });
      session.index = idx >= 0 ? idx : session.index;""",
"""      const ids = currentAdaptiveQuestionIds(session.responses);
      const idx = ids.findIndex((qid) => !session.responses[qid]);
      session.index = idx >= 0 ? idx : session.index;"""
)

start = s.index("function buildSheetEmbed(")
end = s.index("\nfunction buildResultsEmbed", start)

new = """function buildSheetEmbed(user, sheet, aegis = null) {
  const profile = buildComicProfile(sheet);

  return new EmbedBuilder()
    .setTitle(profile.title)
    .setDescription(
      `**${profile.subtitle}**\\n\\n` +
      `${profile.cover_line}`
    )
    .addFields(
      {
        name: 'Back-of-Comic Profile',
        value: profile.stat_blocks.join('\\n'),
        inline: false
      },
      {
        name: 'AEGIS Readout',
        value: aegis?.summary || 'The subject has completed classification. A locked manifestation profile has been generated.',
        inline: false
      },
      {
        name: 'Field Notes',
        value: profile.back_matter.join('\\n'),
        inline: false
      }
    )
    .setColor(0xf1c40f)
    .setFooter({ text: 'Raw scoring matrix sealed under AEGIS protocol.' });
}
"""

s = s[:start] + new + s[end:]

p.write_text(s)
print("DISCORD_ADAPTIVE_PATCH=PASS")
PY

cat > tests/e2e/adaptive_discord_model.e2e.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  adaptiveProgress,
  currentAdaptiveQuestionIds
} from '../../packages/scoring-engine/index.js';

test('discord adaptive model starts with 36 domain questions', () => {
  const responses = {};
  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.length, 36);
  assert.equal(progress.total, 36);
  assert.equal(progress.remaining, 36);
});

test('discord adaptive model expands to only velocity block after velocity manifests', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'C';

  const ids = currentAdaptiveQuestionIds(responses);
  const progress = adaptiveProgress(responses);

  assert.equal(ids.length, 42);
  assert.deepEqual(ids.slice(-6), [43, 44, 45, 46, 47, 48]);
  assert.equal(progress.answered, 36);
  assert.equal(progress.remaining, 6);
});

test('discord adaptive model completes after manifested block only', () => {
  const responses = {};
  for (let i = 1; i <= 36; i++) responses[i] = 'C';
  for (let i = 43; i <= 48; i++) responses[i] = 'F';

  const progress = adaptiveProgress(responses);

  assert.equal(progress.total, 42);
  assert.equal(progress.answered, 42);
  assert.equal(progress.remaining, 0);
  assert.equal(progress.complete, true);
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test:adaptive-discord"] = "node --test tests/e2e/adaptive_discord_model.e2e.test.js"
scripts["test:mobile"] = "npm run test:contract && npm run test:e2e && npm run test:scoring && npm run test:adaptive && npm run test:adaptive-progress && npm run test:adaptive-discord"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("ADAPTIVE_DISCORD_TEST_SCRIPT=PASS")
PY

npm run test:adaptive-discord
npm run test:mobile

echo "WIRE_ADAPTIVE_QUIZ_TO_DISCORD=PASS"
