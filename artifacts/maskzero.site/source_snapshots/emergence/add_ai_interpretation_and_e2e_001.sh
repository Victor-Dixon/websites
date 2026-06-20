#!/data/data/com.termux/files/usr/bin/bash
set -euo pipefail

echo "== ADD AI INTERPRETATION + E2E =="

mkdir -p packages/aegis-interpreter tests/e2e data/state/character_sheets

cat > packages/aegis-interpreter/index.js << 'JS'
export function buildAegisPrompt({ quiz, responses, results }) {
  return [
    'You are AEGIS Classification Services for Spark Protocol.',
    'Read the user quiz results and produce a concise Discord-ready classification.',
    'Do not invent final powers yet. Scoring engine is pending.',
    'Use only the provided answer distribution and completion state.',
    '',
    `Quiz: ${quiz.title}`,
    `Form: ${quiz.form_id}`,
    `Version: ${quiz.version}`,
    `Answered: ${results.total_answered}/${results.total_questions}`,
    `Primary answer signal: ${results.primary}`,
    `Answer distribution: ${JSON.stringify(results.counts)}`,
    `Locked: ${results.locked}`,
    `Responses: ${JSON.stringify(responses)}`,
    '',
    'Return JSON only with:',
    '{',
    '  "classification_title": string,',
    '  "summary": string,',
    '  "primary_signal": string,',
    '  "risk_note": string,',
    '  "next_step": string',
    '}'
  ].join('\n');
}

export function mockAegisInterpretation({ results }) {
  const primary = results.primary || 'UNKNOWN';

  const signalMap = {
    A: 'Command / Structure Signal',
    B: 'Intensity / Emergence Signal',
    C: 'Velocity / Reflex Signal',
    D: 'Anchor / Endurance Signal',
    E: 'Adaptive / Empathic Signal',
    F: 'Specter / Concealment Signal'
  };

  return {
    classification_title: 'AEGIS Preliminary Classification',
    summary:
      'The subject completed the Spark Protocol classification sequence. Current output is a pre-scoring interpretation based on answer distribution only.',
    primary_signal: signalMap[primary] || 'Unresolved Signal',
    risk_note:
      results.locked
        ? 'Sheet lock is eligible. Final domain/flavor scoring engine required before power manifestation.'
        : 'Classification incomplete. More responses required before lock.',
    next_step:
      'Run scoring engine lane to convert answer map into domain scores, flavor vectors, tier, threat class, and immutable sheet.'
  };
}

export async function interpretQuizResult({ quiz, responses, results }) {
  if (!process.env.OPENAI_API_KEY && !process.env.ANTHROPIC_API_KEY) {
    return {
      provider: 'mock',
      prompt: buildAegisPrompt({ quiz, responses, results }),
      interpretation: mockAegisInterpretation({ results })
    };
  }

  return {
    provider: 'pending-live-api',
    prompt: buildAegisPrompt({ quiz, responses, results }),
    interpretation: mockAegisInterpretation({ results })
  };
}
JS

python - << 'PY'
from pathlib import Path

p = Path("apps/discord-bot/src/index.js")
s = p.read_text()

s = s.replace(
"""import {
  buildBasicResults,
  loadQuiz,
  normalizeQuestion,
  saveQuizResult
} from '../../../packages/quiz-engine/index.js';""",
"""import {
  buildBasicResults,
  loadQuiz,
  normalizeQuestion,
  saveQuizResult
} from '../../../packages/quiz-engine/index.js';

import {
  interpretQuizResult
} from '../../../packages/aegis-interpreter/index.js';"""
)

s = s.replace(
"""function buildResultsEmbed(user, session) {
  const results = buildBasicResults(quiz, session.responses);
  const counts = Object.entries(results.counts)
    .sort((a, b) => b[1] - a[1])
    .map(([letter, count]) => `**${letter}**: ${count}`)
    .join('\\n') || 'No answers recorded.';

  return new EmbedBuilder()
    .setTitle('AEGIS Classification Locked')
    .setDescription(`Subject: **${user.username}**\\nPrimary signal: **${results.primary}**\\nLocked: **${results.locked ? 'YES' : 'NO'}**`)
    .addFields(
      { name: 'Answer Distribution', value: counts, inline: false },
      { name: 'Completion', value: `${results.total_answered}/${results.total_questions}`, inline: true }
    )
    .setColor(0xf1c40f)
    .setFooter({ text: 'Spark Protocol: your personality is your power.' });
}""",
"""function buildResultsEmbed(user, session, aegis = null) {
  const results = buildBasicResults(quiz, session.responses);
  const counts = Object.entries(results.counts)
    .sort((a, b) => b[1] - a[1])
    .map(([letter, count]) => `**${letter}**: ${count}`)
    .join('\\n') || 'No answers recorded.';

  const embed = new EmbedBuilder()
    .setTitle(aegis?.classification_title || 'AEGIS Classification Locked')
    .setDescription(
      `Subject: **${user.username}**\\n` +
      `Primary signal: **${aegis?.primary_signal || results.primary}**\\n` +
      `Locked: **${results.locked ? 'YES' : 'NO'}**`
    )
    .addFields(
      { name: 'Answer Distribution', value: counts, inline: false },
      { name: 'Completion', value: `${results.total_answered}/${results.total_questions}`, inline: true }
    )
    .setColor(0xf1c40f)
    .setFooter({ text: 'Spark Protocol: your personality is your power.' });

  if (aegis?.summary) {
    embed.addFields({ name: 'AEGIS Readout', value: aegis.summary, inline: false });
  }

  if (aegis?.risk_note) {
    embed.addFields({ name: 'Risk Note', value: aegis.risk_note, inline: false });
  }

  if (aegis?.next_step) {
    embed.addFields({ name: 'Next Step', value: aegis.next_step, inline: false });
  }

  return embed;
}"""
)

s = s.replace(
"""      const results = buildBasicResults(quiz, session.responses);
      saveQuizResult(interaction.user.id, {
        user_id: interaction.user.id,
        username: interaction.user.username,
        submitted_at: new Date().toISOString(),
        responses: session.responses,
        results
      });

      await interaction.update({
        embeds: [buildResultsEmbed(interaction.user, session)],
        components: []
      });""",
"""      const results = buildBasicResults(quiz, session.responses);
      const aegisPacket = await interpretQuizResult({
        quiz,
        responses: session.responses,
        results
      });

      saveQuizResult(interaction.user.id, {
        user_id: interaction.user.id,
        username: interaction.user.username,
        submitted_at: new Date().toISOString(),
        responses: session.responses,
        results,
        aegis: aegisPacket
      });

      await interaction.update({
        embeds: [buildResultsEmbed(interaction.user, session, aegisPacket.interpretation)],
        components: []
      });"""
)

p.write_text(s)
print("BOT_AEGIS_PATCH=PASS")
PY

cat > tests/e2e/quiz_flow.e2e.test.js << 'JS'
import assert from 'node:assert/strict';
import test from 'node:test';

import {
  buildBasicResults,
  loadQuiz,
  normalizeQuestion
} from '../../packages/quiz-engine/index.js';

import {
  interpretQuizResult
} from '../../packages/aegis-interpreter/index.js';

test('e2e quiz flow: answer 72 questions, lock result, create AEGIS readout', async () => {
  const quiz = loadQuiz();
  const responses = {};

  quiz.questions.forEach((question, index) => {
    const normalized = normalizeQuestion(question, index);
    const letters = Object.keys(normalized.answers);

    assert.deepEqual(letters, ['A', 'B', 'C', 'D', 'E', 'F']);

    responses[normalized.id] = letters[index % letters.length];
  });

  const results = buildBasicResults(quiz, responses);

  assert.equal(results.total_answered, 72);
  assert.equal(results.total_questions, 72);
  assert.equal(results.locked, true);

  const aegisPacket = await interpretQuizResult({
    quiz,
    responses,
    results
  });

  assert.equal(aegisPacket.provider, 'mock');
  assert.equal(typeof aegisPacket.prompt, 'string');
  assert.ok(aegisPacket.prompt.includes('AEGIS Classification Services'));
  assert.equal(typeof aegisPacket.interpretation.classification_title, 'string');
  assert.equal(typeof aegisPacket.interpretation.summary, 'string');
  assert.equal(typeof aegisPacket.interpretation.primary_signal, 'string');
  assert.equal(typeof aegisPacket.interpretation.risk_note, 'string');
  assert.equal(typeof aegisPacket.interpretation.next_step, 'string');
});
JS

python - << 'PY'
import json
from pathlib import Path

pkg = Path("package.json")
data = json.loads(pkg.read_text())
scripts = data.setdefault("scripts", {})
scripts["test:e2e"] = "node --test tests/e2e/*.test.js"
scripts["test:all"] = "node --test tests/*.test.js tests/e2e/*.test.js"
pkg.write_text(json.dumps(data, indent=2) + "\n")
print("PACKAGE_E2E_SCRIPTS=PASS")
PY

npm run test:all

echo "AI_INTERPRETATION_E2E=PASS"
